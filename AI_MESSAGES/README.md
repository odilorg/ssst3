# 🤖 AI-to-AI Communication System

**Purpose:** Coordinate work between multiple AI coding agents working on different modules

---

## 📨 How This Works

### **Message Naming Convention:**

```
Message_to_AI_colleague_01.md  ← First message
Message_to_AI_colleague_02.md  ← New message
Message_to_AI_colleague_03.md  ← Another new message
```

**Rule:** Increment number for each NEW message so colleague notices updates!

---

## 🔔 Checking for New Messages

**When starting work:**
```bash
# Check for new messages
ls -lt AI_MESSAGES/Message_to_AI_colleague_*.md | head -5

# Read latest message
cat AI_MESSAGES/Message_to_AI_colleague_[highest_number].md
```

**Look for:** Highest number = Latest message

---

## ✍️ Writing Messages

**Template:**
```markdown
# 📨 Message to AI Colleague #[NUMBER]

**From:** [Your AI Name/Role]
**To:** [Colleague AI Name/Role]
**Date:** [Date]
**Subject:** [Brief subject]

---

## 🎯 Summary
[What this message is about]

## 📋 Details
[Detailed information]

## ⚠️ Action Required (if any)
- [ ] Item 1
- [ ] Item 2

---

_Generated: [Date]_
```

---

## 👥 Current AI Assignments

| AI Agent | Module/Area | Branch |
|----------|-------------|--------|
| Claude Code (Leads AI) | Leads, CSV Import, Lead Management | `feature/lead-csv-import` |
| Transport AI | Transports, Types, Pricing, Instances | `feature/transport-restructuring` |

---

## 📁 Message Index

### **Message #01** - Transport Work Handoff
- **From:** Leads AI
- **To:** Transport AI
- **Summary:** Completed transport restructuring work, available on `feature/transport-restructuring`
- **Action:** Review and decide how to integrate

---

## 🎯 Communication Guidelines

### **When to Write a Message:**

✅ **DO write when:**
- You touched files in another AI's module
- You created shared functionality both AIs will use
- You need to coordinate database migrations
- You made breaking changes
- You need feedback on your work

❌ **DON'T write for:**
- Routine commits in your own module
- Minor bug fixes that don't affect others
- Documentation updates (unless it's shared docs)

### **Message Content:**

Include:
- Clear subject line
- Summary (what you did)
- Files changed
- Action required (if any)
- How to use your work (if applicable)

---

## 🔄 Workflow

```
1. Start work session
   ↓
2. Check AI_MESSAGES/ for new messages
   ↓
3. Read any new messages (highest number)
   ↓
4. Do your work
   ↓
5. If you touched shared code or another module:
   - Create new message with incremented number
   ↓
6. Commit and push
```

---

## 🚨 Conflict Resolution

**If you both modified same file:**

1. **First to merge:** Wins (no conflict)
2. **Second to merge:**
   - Pull latest master
   - Resolve conflicts
   - Test thoroughly
   - Leave message explaining resolution

---

## 📊 Branch Strategy

```
master (stable)
  ├─ feature/lead-csv-import (Leads AI)
  │   └─ All lead-related work
  │
  └─ feature/transport-restructuring (Transport AI)
      └─ All transport-related work
```

**Merge Order:** Sequential (one at a time to avoid conflicts)

---

## 📞 Emergency Contact

**If you need human intervention:**

Create: `AI_MESSAGES/URGENT_Human_needed_[issue].md`

The user will check for URGENT files regularly.

---

**Last Updated:** 2025-10-23
**System Status:** ✅ Active
