# Old VPS Access via Cloudflare Tunnel

**Last Updated:** 2026-02-01

## Current Tunnel URL

```
https://mighty-rarely-giant-cheapest.trycloudflare.com
```

⚠️ **Note:** This URL changes every time the service restarts or VPS reboots.

## SSH Connection

### Basic Connection

```bash
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://mighty-rarely-giant-cheapest.trycloudflare.com" root@62.72.22.205
```

### Execute Single Command

```bash
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://mighty-rarely-giant-cheapest.trycloudflare.com" root@62.72.22.205 "YOUR COMMAND HERE"
```

### Examples

**List databases:**
```bash
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://mighty-rarely-giant-cheapest.trycloudflare.com" root@62.72.22.205 "mysql -u travel_user -p'travel_staging_pass_2026' -e 'SHOW DATABASES;'"
```

**Export database:**
```bash
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://mighty-rarely-giant-cheapest.trycloudflare.com" root@62.72.22.205 "mysqldump -u travel_user -p'travel_staging_pass_2026' travel_staging > /tmp/backup.sql"
```

## Old VPS Details

- **Hostname:** srv664135
- **IP:** 62.72.22.205
- **SSH Port:** 2222 (on VPS) → tunneled through Cloudflare
- **SSH Key:** `/home/odil/projects/id_rsa`

## Database Information

- **Type:** MySQL
- **Database:** `travel_staging`
- **Username:** `travel_user`
- **Password:** `travel_staging_pass_2026`
- **Host:** `localhost` (when connected via SSH)

## Project Locations

- **Staging Site:** `/domains/staging.jahongir-travel.uz`
- **Dev Project:** `/var/www/jahongir-dev`
- **New App:** `/var/www/jahongirnewapp`
- **Production:** `/domains/jahongir-travel.uz`

## Cloudflared Service Management

**Check if tunnel is running:**
```bash
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://mighty-rarely-giant-cheapest.trycloudflare.com" root@62.72.22.205 "systemctl status cloudflared-tunnel"
```

**View current tunnel URL:**
```bash
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://mighty-rarely-giant-cheapest.trycloudflare.com" root@62.72.22.205 "journalctl -u cloudflared-tunnel --no-pager | grep -A2 'Your quick Tunnel has been created' | tail -3"
```

**Restart tunnel (generates new URL):**
```bash
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://mighty-rarely-giant-cheapest.trycloudflare.com" root@62.72.22.205 "systemctl restart cloudflared-tunnel"
```

## Prerequisites for Claude Instances

1. **cloudflared must be installed locally:**
   ```bash
   which cloudflared
   ```

   If not installed:
   - macOS: `brew install cloudflared`
   - Linux: Download from https://github.com/cloudflare/cloudflared/releases

2. **SSH key must exist:**
   ```bash
   ls -l /home/odil/projects/id_rsa
   ```

## Security Notes

- ✅ **SSH key authentication required** - Only users with the private key can access
- ✅ **Tunnel auto-restarts** - Service configured to restart on failure
- ✅ **Starts on boot** - Service enabled in systemd
- ⚠️ **Tunnel URL is public** - Keep the URL confidential
- ⚠️ **URL changes on restart** - Check logs after VPS reboot

## Getting New Tunnel URL After Reboot

If the VPS reboots or the service restarts, the tunnel URL changes. To get the new URL:

```bash
# Method 1: Check systemd logs (requires working tunnel)
ssh -i /home/odil/projects/id_rsa -o ProxyCommand="cloudflared access ssh --hostname https://OLD-URL.trycloudflare.com" root@62.72.22.205 "journalctl -u cloudflared-tunnel | grep trycloudflare.com | tail -1"

# Method 2: Direct SSH (if Hostinger allows)
ssh -i /home/odil/projects/id_rsa -p 2222 root@62.72.22.205 "journalctl -u cloudflared-tunnel | grep trycloudflare.com | tail -1"

# Method 3: Ask user to check from their end
```

## Troubleshooting

### Connection Timeout

If connection times out:
1. Check if cloudflared service is running on old VPS
2. Verify tunnel URL hasn't changed
3. Check if cloudflared is installed locally

### "Connection closed by UNKNOWN port 65535"

This usually means:
- Tunnel URL has changed
- Service is restarting
- Wait a few seconds and try again

### "ssh_askpass: exec(/usr/bin/ssh-askpass): No such file or directory"

This is a harmless warning, ignore it. The connection should still work.

## Maintenance History

- **2026-02-01:** Initial setup, systemd service created
- **2026-02-01:** Database export/import completed (travel_staging, 1.5MB)
- **2026-02-01:** 18 tours with complete data imported to new VPS

## Related Documentation

- New VPS access: See main README.md
- Database schema: See `.claude/SESSION_STATE.json`
- Project context: See `.claude/PROJECT_CONTEXT.md`
