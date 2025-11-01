# Hook Error Fix Documentation

**Date:** November 1, 2025
**Issue:** "Stop hook error" message appearing in Claude Code
**Priority:** 🟡 Medium

---

## Problem Description

The "Stop hook error" message was appearing when Claude Code sessions ended. This was caused by hooks configured in `~/.claude/settings.json` that were failing to execute properly.

## Root Cause

The `Stop` and `UserPromptSubmit` hooks were attempting to check for tasks in the `.swarm/agents/agent1/inbox/` directory without:
1. Verifying the directory exists before checking
2. Properly handling errors with `exit 0`
3. Using error suppression with `2>/dev/null`

### Original Hook (Problematic)
```bash
bash -c 'task_count=$(ls /c/Users/Admin/.swarm/agents/agent1/inbox/*.json 2>/dev/null | wc -l); if [ "$task_count" -gt 0 ]; then echo "📬 REMINDER: $task_count task(s) still pending in inbox."; fi'
```

**Issues:**
- No directory existence check
- No explicit `exit 0` to ensure clean exit
- Could fail if directory structure doesn't exist

## Solution

Updated hooks to include:
1. **Directory existence check** before attempting to list files
2. **Explicit exit 0** to ensure clean hook termination
3. **Variable for directory path** for better readability

### Fixed Hook
```bash
bash -c 'INBOX_DIR="/c/Users/Admin/.swarm/agents/agent1/inbox"; if [ -d "$INBOX_DIR" ]; then task_count=$(ls "$INBOX_DIR"/*.json 2>/dev/null | wc -l); if [ "$task_count" -gt 0 ]; then echo "📬 REMINDER: $task_count task(s) still pending in inbox."; fi; fi; exit 0'
```

## Files Modified

### `~/.claude/settings.json`

**Stop Hook (lines 23-32):**
```diff
"Stop": [
  {
    "hooks": [
      {
        "type": "command",
-       "command": "bash -c 'task_count=$(ls /c/Users/Admin/.swarm/agents/agent1/inbox/*.json 2>/dev/null | wc -l); if [ \"$task_count\" -gt 0 ]; then echo \"📬 REMINDER: $task_count task(s) still pending in inbox.\"; fi'"
+       "command": "bash -c 'INBOX_DIR=\"/c/Users/Admin/.swarm/agents/agent1/inbox\"; if [ -d \"$INBOX_DIR\" ]; then task_count=$(ls \"$INBOX_DIR\"/*.json 2>/dev/null | wc -l); if [ \"$task_count\" -gt 0 ]; then echo \"📬 REMINDER: $task_count task(s) still pending in inbox.\"; fi; fi; exit 0'"
      }
    ]
  }
]
```

**UserPromptSubmit Hook (lines 13-22):**
```diff
"UserPromptSubmit": [
  {
    "hooks": [
      {
        "type": "command",
-       "command": "bash -c '...task_count=$(ls /c/Users/Admin/.swarm/agents/agent1/inbox/*.json 2>/dev/null | wc -l); if [ \"$task_count\" -gt 0 ]; then echo \"⚠️ INBOX: $task_count pending task(s) - Should I process them?\"; fi'"
+       "command": "bash -c '...INBOX_DIR=\"/c/Users/Admin/.swarm/agents/agent1/inbox\"; if [ -d \"$INBOX_DIR\" ]; then task_count=$(ls \"$INBOX_DIR\"/*.json 2>/dev/null | wc -l); if [ \"$task_count\" -gt 0 ]; then echo \"⚠️ INBOX: $task_count pending task(s) - Should I process them?\"; fi; fi; exit 0'"
      }
    ]
  }
]
```

## Verification Steps

### Test the Stop hook manually:
```bash
bash -c 'INBOX_DIR="/c/Users/Admin/.swarm/agents/agent1/inbox"; if [ -d "$INBOX_DIR" ]; then task_count=$(ls "$INBOX_DIR"/*.json 2>/dev/null | wc -l); if [ "$task_count" -gt 0 ]; then echo "📬 REMINDER: $task_count task(s) still pending in inbox."; fi; fi; exit 0'
```

Expected output:
- If inbox has tasks: `📬 REMINDER: X task(s) still pending in inbox.`
- If inbox is empty: No output (silent success)
- If directory doesn't exist: No output, no error (silent success)

### Test in Claude Code:
1. Start a new Claude Code session
2. Type a message and send
3. Press ESC to stop
4. Verify no "Stop hook error" appears

## Prevention Guidelines

When writing Claude Code hooks:

1. **Always check for file/directory existence:**
   ```bash
   if [ -d "$DIR" ]; then
     # Your code here
   fi
   ```

2. **Always add explicit exit 0:**
   ```bash
   bash -c 'your_command; exit 0'
   ```

3. **Suppress errors with 2>/dev/null:**
   ```bash
   ls "$DIR"/*.json 2>/dev/null
   ```

4. **Use variables for paths:**
   ```bash
   INBOX_DIR="/path/to/inbox"
   if [ -d "$INBOX_DIR" ]; then
     # Work with $INBOX_DIR
   fi
   ```

5. **Test hooks in isolation before adding to settings:**
   ```bash
   bash -c 'YOUR_HOOK_COMMAND' && echo "✓ Success"
   ```

## Related Documentation

- Claude Code Hooks: https://docs.claude.com/en/docs/claude-code/hooks
- Bash Error Handling: Best practices for shell scripts
- Hook Error Troubleshooting: Check `~/.claude/debug/` logs

## Impact

✅ **Fixed:** No more "Stop hook error" messages
✅ **Improved:** Hooks are more robust and fail gracefully
✅ **Verified:** All hooks tested and working correctly

## Maintainer Notes

- If adding new hooks that interact with the filesystem, follow prevention guidelines
- Test hooks in isolation before deploying
- Consider adding timeout configuration for long-running hooks
- Document any new hooks in this file or project-specific docs
