# External System Integration (Hospital EMR)

This EMR supports two integration mechanisms:

1. **Long-lived API keys** — stable credentials for system-to-system calls (no 60-hour JWT expiry).
2. **Signed webhooks** — authenticated event delivery in both directions.

## 1. API keys

### Create a key on the server

```bash
cd /home/ubuntu/HOSPITAL-EMR
docker compose exec api php artisan api-key:create "Hospital EMR Integration"
```

Optional flags:

```bash
docker compose exec api php artisan api-key:create "Hospital EMR Integration" --user=admin@hospital.bo --ability=patients.view --ability=patients.create --expires=+30\ days
```

- `--user` accepts a user ID or email. Defaults to the first `super_admin` user.
- `--ability` can be repeated. Omit it to grant `*` (full access).
- `--expires` accepts any parseable date; omit it for a key that never expires.

The plaintext key is printed **once**. It is stored only as a SHA-256 hash.

### Create a key through the API

```bash
curl -X POST http://52.62.184.84:3000/api/v1/api-keys \
  -H 'Authorization: Bearer <JWT_OR_API_KEY>' \
  -H 'Content-Type: application/json' \
  -d '{"name":"Hospital EMR Integration","abilities":["*"]}'
```

### Use the key

Send it on **any** existing API route (JWT is still accepted):

```bash
curl -H 'X-API-Key: emr_xxx...' http://52.62.184.84:3000/api/v1/patients
# or
curl -H 'Authorization: Bearer emr_xxx...' http://52.62.184.84:3000/api/v1/patients
```

### Manage keys

| Method | Endpoint | Access |
|---|---|---|
| GET | `/api/v1/api-keys` | Own keys; admins see their institution / all |
| POST | `/api/v1/api-keys` | Any authenticated user (own key); admins may create for others |
| DELETE | `/api/v1/api-keys/{id}` | Owner or admin |

Revoking deletes the record; the key stops working immediately.

## 2. Webhooks

### Inbound (other system → this EMR)

Endpoint: `POST http://52.62.184.84:3000/api/v1/webhooks/hospital-emr`

Required headers:

| Header | Description |
|---|---|
| `X-Webhook-Signature` | `sha256=<hmac_sha256_hex>` |
| `X-Webhook-Timestamp` | Unix timestamp; signed payload is `<timestamp>.<raw body>` |
| `X-Webhook-Id` | Unique event ID, used for idempotency |
| `X-Webhook-Event` | Event name, e.g. `patient.updated` |

The secret is `HOSPITAL_EMR_WEBHOOK_VERIFY_SECRET` on this EMR. Same-secret
counterpart on the other system is `HOSPITAL_EMR_WEBHOOK_SIGNING_SECRET`.

Example signature generation:

```bash
BODY='{"id":"evt-123","event":"patient.updated","data":{"id":7}}'
TS=$(date +%s)
SIG=$(printf '%s.%s' "$TS" "$BODY" | openssl dgst -sha256 -hmac 'SHARED_SECRET' | awk '{print $2}')
curl -X POST http://52.62.184.84:3000/api/v1/webhooks/hospital-emr \
  -H 'Content-Type: application/json' \
  -H "X-Webhook-Timestamp: $TS" \
  -H "X-Webhook-Signature: sha256=$SIG" \
  -H 'X-Webhook-Id: evt-123' \
  -H 'X-Webhook-Event: patient.updated' \
  -d "$BODY"
```

- Bad/missing signature → `401`; timestamp older than 5 minutes → `401`.
- Duplicate `X-Webhook-Id` → `200` with `duplicate: true` (not stored twice).
- Valid event → `201` and stored in `webhook_events`.

Admins can inspect received events:

- `GET /api/v1/webhooks/events`
- `GET /api/v1/webhooks/events/{id}`
- `PATCH /api/v1/webhooks/events/{id}/processed`
- `POST /api/v1/webhooks/test` — sends a signed test event to `HOSPITAL_EMR_WEBHOOK_URL`

### Outbound (this EMR → other system)

Set `HOSPITAL_EMR_WEBHOOK_URL` and `HOSPITAL_EMR_WEBHOOK_SIGNING_SECRET`.
Payload format:

```json
{
  "id": "<uuid>",
  "event": "patient.updated",
  "created_at": "<ISO-8601>",
  "data": { "...": "..." }
}
```

Signed exactly as above, using `HOSPITAL_EMR_WEBHOOK_SIGNING_SECRET`, and
sent with `X-API-Key: <HOSPITAL_EMR_OUTBOUND_API_KEY>` when that is set.

## 3. Environment variable mapping

| Variable (other system) | Value |
|---|---|
| `HOSPITAL_EMR_BASE_URL` | `http://52.62.184.84:3000/api/v1` |
| `HOSPITAL_EMR_OUTBOUND_API_KEY` | API key generated on this EMR |
| `HOSPITAL_EMR_INBOUND_API_KEY` | Provided by the other system |
| `HOSPITAL_EMR_WEBHOOK_SIGNING_SECRET` | Same value as this EMR's `HOSPITAL_EMR_WEBHOOK_VERIFY_SECRET` |
| `HOSPITAL_EMR_WEBHOOK_VERIFY_SECRET` | Same value as this EMR's `HOSPITAL_EMR_WEBHOOK_SIGNING_SECRET` |
| `HOSPITAL_EMR_WEBHOOK_URL` | `http://52.62.184.84:3000/api/v1/webhooks/hospital-emr` |

On this EMR the same six variables exist in `docker-compose.yml` and are
written into the container's `.env` by `docker-entrypoint.sh` at startup.

## 4. Deploy changes

```bash
cd /home/ubuntu/HOSPITAL-EMR
docker compose up -d --build api
docker compose exec api php artisan api-key:create "Hospital EMR Integration"
```

The entrypoint runs `php artisan migrate --force` automatically, creating the
`webhook_events` table. No manual migration command is needed.

## 5. Security notes

- API keys are hashed at rest; the plaintext cannot be recovered.
- Prefer a dedicated integration user over the super-admin account.
- Use distinct signing and verify secrets (one per direction).
- Rotate keys by revoking and creating a new one.
- Keep secrets out of git; set them through environment variables only.
