# Freedcamp Integration - Jahongir Travel Website

> **Project-specific Freedcamp tools for jahongir-travel-staging**

## Overview

Manage tasks for the Jahongir Travel Website project in Freedcamp directly from Claude Code.

## Tools

### 1. freedcamp-task - Create Tasks

**Location:** `./.claude/freedcamp/freedcamp-task`

**Usage:**
```bash
# From project root
./.claude/freedcamp/freedcamp-task "Task title" -d "Description" --due 2026-02-10

# Options:
#   -d, --description   Task description (HTML supported)
#   -p, --priority      0=none, 1=low, 2=medium, 3=high
#   -a, --assignee      User ID or name
#   --due               Due date (YYYY-MM-DD)
#   --list-tasks        List all tasks
#   --list-users        List project users
```

### 2. freedcamp-status - Check Progress

**Location:** `./.claude/freedcamp/freedcamp-status`

**Usage:**
```bash
# Show overall status
./.claude/freedcamp/freedcamp-status

# Show tasks with comments
./.claude/freedcamp/freedcamp-status --comments

# Show bug reports
./.claude/freedcamp/freedcamp-status --bugs
```

## Configuration

**Project config:** `./.claude/freedcamp/config.json`
- Project: "Jahongir-travel-website"
- Uses global Freedcamp credentials from `~/.config/freedcamp/credentials.json`

**Separate from:** Clinic Saas project (different project ID)

## Setup Instructions

### Get Project ID

Run this command to get your project ID:
```bash
./.claude/freedcamp/freedcamp-task --list-projects
```

Or manually:
1. Go to Freedcamp project
2. Click on project settings
3. Copy the project ID from URL

Then update `./.claude/freedcamp/config.json`:
```json
{
  "project_id": "YOUR_PROJECT_ID_HERE",
  "project_name": "Jahongir-travel-website"
}
```

## Quick Reference

```bash
# Create task
./.claude/freedcamp/freedcamp-task "Implement booking form" -p 3

# Check status
./.claude/freedcamp/freedcamp-status

# List all tasks
./.claude/freedcamp/freedcamp-task --list-tasks
```
