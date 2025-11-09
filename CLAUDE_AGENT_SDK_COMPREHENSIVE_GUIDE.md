# Claude Agent SDK - Comprehensive Guide

## Table of Contents
1. [Overview](#overview)
2. [Input Modes](#input-modes)
3. [Sessions](#sessions)
4. [Permissions](#permissions)
5. [System Prompts](#system-prompts)
6. [Hosting](#hosting)
7. [MCP (Model Context Protocol)](#mcp-model-context-protocol)
8. [Custom Tools](#custom-tools)
9. [Subagents](#subagents)
10. [Slash Commands](#slash-commands)
11. [Skills](#skills)
12. [Todo Tracking](#todo-tracking)

---

## Overview

The Claude Agent SDK is a powerful framework for building AI-powered applications and agents. It provides tools for:

- **Tool Usage**: Leverage Claude's ability to use external tools
- **Session Management**: Maintain conversation state and context
- **File System Operations**: Read, write, and edit files
- **Code Execution**: Run bash commands and scripts
- **Custom Extensions**: Build custom tools, subagents, and skills
- **Permission Control**: Fine-grained control over what the agent can do

**Key Requirements:**
- Python 3.10+ or Node.js 18+
- Container-based sandboxing for security
- Outbound HTTPS access to `api.anthropic.com`

---

## Input Modes

The Claude Agent SDK supports two input modes for handling different use cases.

### 1. Streaming Input Mode (Recommended)

**Default and preferred mode for interactive applications.**

#### Features:
- ✅ **Persistent sessions** with full context
- ✅ **Image uploads** for visual analysis
- ✅ **Queued messages** with sequential processing
- ✅ **Tool integration** with all tools and MCP servers
- ✅ **Hooks support** for lifecycle customization
- ✅ **Real-time feedback** as responses generate
- ✅ **Natural multi-turn conversations**

#### Example (TypeScript):
```typescript
import { query } from "@anthropic-ai/claude-agent-sdk";

const messages = [];

for await (const message of query({
  prompt: "Add a new React component for user profiles",
  options: {
    // Configuration options
  },
})) {
  messages.push(message);
  if (message.type === "assistant") {
    console.log(message.message.content);
  }
}
```

#### Example (Python):
```python
from claude_agent_sdk import query

async for message in query(
    prompt="Add a new React component for user profiles",
    options={}
):
    if message.type == "assistant":
        print(message.message.content)
```

### 2. Single Message Input

**Simpler approach for one-shot queries.**

#### When to Use:
- One-shot responses needed
- No image attachments required
- No hooks needed
- Stateless environments (e.g., lambda functions)

#### Limitations:
- ❌ No image attachments
- ❌ No message queueing
- ❌ No real-time interruption
- ❌ No hook integration
- ❌ No natural multi-turn conversations

#### Example:
```typescript
// One-shot query
const result = await query({
  prompt: "Explain how a binary search algorithm works"
});
```

---

## Sessions

### Overview
Sessions maintain conversation state and enable resuming previous interactions.

### Key Features:
- **Automatic Creation**: Sessions created automatically for new queries
- **Session ID**: Captured from initial system message
- **Resume Capability**: Continue from previous conversation state
- **Forking**: Create new branches from existing sessions

### Getting Session ID
```typescript
// The first message is a system init message with session ID
for await (const message of query({ prompt: "Hello" })) {
  if (message.type === "system_init") {
    const sessionId = message.session_id;
    // Save sessionId for later
  }
}
```

### Resuming Sessions
```typescript
const result = await query({
  prompt: "Continue our conversation",
  options: {
    resume: "session-id-here"  // Resume previous session
  }
});
```

### Forking Sessions
```typescript
// Continue original session
const continueResult = await query({
  prompt: "Continue here",
  options: { resume: "session-id" }
});

// Fork into new branch
const forkedResult = await query({
  prompt: "Try a different approach",
  options: {
    resume: "session-id",
    forkSession: true  // Creates new session ID
  }
});
```

### When to Fork vs Continue

**Continue (forkSession: false):**
- Sequential development workflow
- Natural conversation flow
- Building on previous work
- Maintaining context

**Fork (forkSession: true):**
- Exploring different approaches
- Testing changes without affecting original
- Creating conversation branches
- A/B testing different strategies

---

## Permissions

### Overview
Four complementary approaches to manage tool usage and security.

### 1. Permission Modes
Global behavior settings affecting all tools.

**Available Modes:**
- `default` - Standard permission checks (default)
- `plan` - Planning mode with read-only tools (not in SDK)
- `acceptEdits` - Auto-accept file edits and filesystem ops
- `bypassPermissions` - Bypass all checks (use with extreme caution)

```typescript
const result = await query({
  prompt: "Fix all syntax errors in the codebase",
  options: {
    permissions: {
      mode: "acceptEdits"  // Auto-approve file modifications
    }
  }
});
```

**Accept Edits Mode Automatically Approves:**
- File edits (Edit, Write tools)
- Bash filesystem commands (mkdir, touch, rm, mv, cp)
- File creation and deletion

**Bypass Mode Effects:**
- ALL tool uses without prompts
- Hooks still execute
- DANGEROUS in production or with sensitive data

### 2. canUseTool Callback
Runtime permission handler for dynamic approval.

```typescript
const result = await query({
  prompt: "Deploy to production",
  options: {
    permissions: {
      canUseTool: (toolName, input) => {
        if (toolName === "Bash" && input.command.includes("rm -rf")) {
          return false;  // Deny dangerous commands
        }
        return true;  // Allow all other tools
      }
    }
  }
});
```

### 3. Hooks
Fine-grained control over every tool execution.

```typescript
const result = await query({
  prompt: "Refactor this module",
  options: {
    hooks: {
      preToolUse: (message) => {
        console.log(`Using tool: ${message.tool_name}`);
        return message;  // Return modified or same message
      },
      postToolUse: (message) => {
        console.log(`Tool completed: ${message.tool_name}`);
        return message;
      }
    }
  }
});
```

### Permission Flow
```
PreToolUse Hook → Deny Rules → Allow Rules → Ask Rules → Permission Mode → canUseTool Callback → PostToolUse Hook
```

### Best Practices
1. Use `default` mode for controlled execution
2. Use `acceptEdits` for isolated work directories
3. Avoid `bypassPermissions` on production systems
4. Combine modes with hooks for fine control
5. Switch modes based on task confidence

---

## System Prompts

### Overview
Four methods to customize Claude's behavior through system prompts.

### Method 1: CLAUDE.md Files (Project-Level)

**Filesystem-based project instructions.**

#### Location:
- Project: `.claude/CLAUDE.md` or `.claude/CLAUDE.md`
- User: `~/.claude/CLAUDE.md`

#### Important: Must Configure Setting Sources
```typescript
const result = await query({
  prompt: "Add user authentication",
  options: {
    systemPrompt: {
      type: "preset",
      preset: "claude_code"  // Use Claude Code's system prompt
    },
    settingSources: ["project"]  // REQUIRED to load CLAUDE.md
  }
});
```

#### Example CLAUDE.md:
```markdown
# Project Guidelines

## Code Style
- Use TypeScript strict mode
- Prefer functional components in React
- Always include JSDoc comments

## Testing
- Run `npm test` before committing
- Maintain >80% code coverage
- Use jest for unit tests

## Commands
- Build: `npm run build`
- Dev: `npm run dev`
- Test: `npm test`
```

#### When to Use:
- ✅ Team-shared context
- ✅ Project conventions
- ✅ Common commands
- ✅ Version-controlled instructions
- ✅ Long-term memory

### Method 2: Output Styles (Persistent Configurations)

**Saved configurations as markdown files.**

#### Creating Output Style:
```typescript
import { writeFile, mkdir } from "fs/promises";
import { join, homedir } from "path";

async function createOutputStyle(name, description, prompt) {
  const outputStylesDir = join(homedir(), ".claude", "output-styles");
  await mkdir(outputStylesDir, { recursive: true });

  const content = `---
name: ${name}
description: ${description}
---

${prompt}`;

  const filePath = join(outputStylesDir, `${name.toLowerCase()}.md`);
  await writeFile(filePath, content, "utf-8");
}
```

#### Activating:
- CLI: `/output-style [style-name]`
- Settings: `.claude/settings.local.json`
- Create: `/output-style:new [description]`

#### With SDK:
```typescript
const result = await query({
  prompt: "Review this code",
  options: {
    settingSources: ["user"]  // Load output styles
  }
});
```

### Method 3: systemPrompt with append

**Add custom instructions while preserving defaults.**

```typescript
const result = await query({
  prompt: "Write a Python function",
  options: {
    systemPrompt: {
      type: "preset",
      preset: "claude_code",
      append: "Always include detailed docstrings and type hints."
    }
  }
});
```

### Method 4: Custom System Prompt

**Complete control over behavior.**

```typescript
const customPrompt = `You are a Python specialist.
Guidelines:
- Write clean, documented code
- Use type hints
- Include docstrings
- Prefer functional patterns`;

const result = await query({
  prompt: "Create a data pipeline",
  options: {
    systemPrompt: customPrompt
  }
});
```

### Comparison

| Feature | CLAUDE.md | Output Styles | append | Custom |
|---------|-----------|---------------|--------|--------|
| Persistence | Per-project | Saved files | Session only | Session only |
| Reusability | Per-project | Cross-project | Code | Code |
| Default tools | Preserved | Preserved | Preserved | Lost |
| Version control | Yes | Yes | No | No |
| Scope | Project | User/Project | Code | Code |

### Best Practices
- **CLAUDE.md**: Team guidelines, project conventions
- **Output Styles**: Persistent behaviors, specialized assistants
- **append**: Add specific standards, formatting
- **Custom**: Complete control, single-session tasks

---

## Hosting

### Requirements
- **Runtime**: Python 3.10+ or Node.js 18+
- **Memory**: 1GiB RAM (minimum)
- **Storage**: 5GiB disk
- **CPU**: 1 core (minimum)
- **Network**: Outbound HTTPS to `api.anthropic.com`
- **Security**: Container-based sandboxing

### Deployment Patterns

#### 1. Ephemeral Sessions
**Create new container per task, destroy when complete.**

```yaml
# Use case: One-off tasks
# Examples: Bug fixes, invoice processing, translations
# Best for: Isolated, independent tasks
```

**Pros:**
- ✅ Complete isolation
- ✅ No state leakage
- ✅ Simple cleanup

**Cons:**
- ❌ Higher overhead
- ❌ No context between tasks
- ❌ Cold starts

#### 2. Long-Running Sessions
**Maintain persistent containers.**

```yaml
# Use case: Continuous operation
# Examples: Proactive agents, email monitoring, site builders
# Best for: Always-on services
```

**Pros:**
- ✅ No cold starts
- ✅ Fast response
- ✅ Maintains context

**Cons:**
- ❌ Higher resource cost
- ❌ State management complexity
- ❌ Potential memory leaks

#### 3. Hybrid Sessions
**Ephemeral containers hydrated with history.**

```yaml
# Use case: Intermittent interaction
# Examples: Project management, research, support
# Best for: Periodic activity
```

**Pros:**
- ✅ Balance of isolation and performance
- ✅ State transfer capability
- ✅ Cost-effective

**Cons:**
- ❌ Complex state management
- ❌ Hydration overhead

#### 4. Single Containers
**Multiple agents in one container.**

```yaml
# Use case: Collaborative agents
# Examples: Multi-agent systems, simulations
# Best for: Tight integration
```

**Pros:**
- ✅ Shared resources
- ✅ Fast inter-agent communication
- ✅ Lower per-agent cost

**Cons:**
- ❌ Shared state
- ❌ Resource contention
- ❌ Isolation concerns

### Cost Considerations
- **Dominant cost**: Tokens (~$0.05/hour minimum for containers)
- **Container timeout**: None, but set `maxTurns` to prevent loops
- **Scaling**: Horizontal (more containers) vs vertical (bigger containers)

### Sandbox Providers
- Cloudflare Sandboxes
- Modal
- Daytona
- E2B
- Fly Machines
- Vercel Sandbox

---

## MCP (Model Context Protocol)

### Overview
MCP servers extend Claude with custom tools and capabilities.

### Transport Types

#### 1. stdio Servers
**External processes via stdin/stdout.**

```json
// .mcp.json
{
  "servers": {
    "my-server": {
      "command": "python",
      "args": ["/path/to/server.py"],
      "env": {
        "API_KEY": "your-key"
      }
    }
  }
}
```

#### 2. HTTP/SSE Servers
**Remote servers with network communication.**

```json
{
  "servers": {
    "remote-api": {
      "url": "https://api.example.com/mcp",
      "headers": {
        "Authorization": "Bearer token"
      }
    }
  }
}
```

#### 3. SDK MCP Servers
**In-process servers within your application.**

```typescript
import { createSdkMcpServer, tool } from "@anthropic-ai/claude-agent-sdk";
import { z } from "zod";

const mcpServer = createSdkMcpServer({
  name: "my-tools",
  version: "1.0.0",
  tools: [
    tool({
      name: "get_weather",
      description: "Get weather for a location",
      inputSchema: z.object({
        location: z.string(),
        units: z.enum(["celsius", "fahrenheit"]).default("celsius")
      }),
      handler: async ({ location, units }) => {
        // Call weather API
        return { location, temperature, conditions };
      }
    })
  ]
});

const result = await query({
  prompt: "What's the weather in Paris?",
  options: {
    mcpServers: {
      "my-tools": mcpServer
    }
  }
});
```

### Features
- **Resource exposure**: MCP servers can expose resources
- **Authentication**: Environment variables supported
- **Error handling**: Graceful failure mechanisms
- **Type safety**: Zod schema validation

---

## Custom Tools

### Overview
In-process MCP servers for specialized operations.

### Creating Custom Tools

```typescript
import { createSdkMcpServer, tool } from "@anthropic-ai/claude-agent-sdk";
import { z } from "zod";

const mcpServer = createSdkMcpServer({
  name: "database-tools",
  version: "1.0.0",
  tools: [
    tool({
      name: "query_database",
      description: "Execute SQL query with safety checks",
      inputSchema: z.object({
        query: z.string(),
        params: z.record(z.any()).optional()
      }),
      handler: async ({ query, params }) => {
        // Validate and execute query
        return { results, rowCount };
      }
    })
  ]
});
```

### Tool Naming Convention
```
mcp__{server_name}__{tool_name}
```
Example: `mcp__database-tools__query_database`

### Using Custom Tools

```typescript
const result = await query({
  prompt: "Get all users who signed up this month",
  options: {
    mcpServers: {
      "database-tools": mcpServer
    },
    allowedTools: ["mcp__database-tools__query_database"]
  }
});
```

### Common Use Cases
- Database query execution
- API gateway services
- Mathematical calculations
- External service integration (Stripe, GitHub, OpenAI)

### Type Safety
- Zod schema validation
- Full TypeScript type inference
- Complex object types, enums, defaults
- Runtime type checking

### Error Handling
```typescript
handler: async (params) => {
  try {
    // Operation
    return { success: true, data };
  } catch (error) {
    return {
      success: false,
      error: error.message
    };
  }
}
```

---

## Subagents

### Overview
Specialized AIs orchestrated by the main agent.

### Benefits
- **Context management**: Separate context prevents overload
- **Parallelization**: Concurrent execution
- **Specialization**: Tailored instructions per agent
- **Tool restrictions**: Limit tools per agent

### Creating Subagents (Programmatically - Recommended)

```typescript
const result = await query({
  prompt: "Review authentication module for security",
  options: {
    agents: {
      "code-reviewer": {
        description: "Expert code review specialist for security and quality",
        prompt: `You are a security-focused code reviewer.
        Focus on: security vulnerabilities, performance issues, coding standards.`,
        tools: ["Read", "Grep", "Glob"],
        model: "sonnet"
      },
      "test-coverage": {
        description: "Analyzes test coverage and quality",
        prompt: "Analyze test coverage and suggest improvements.",
        tools: ["Read", "Bash"],
        model: "haiku"
      }
    }
  }
});
```

### AgentDefinition Configuration
- `description`: When to use this agent
- `prompt`: System prompt defining role
- `tools`: Allowed tools (optional, inherits all if omitted)
- `model`: Model override ('sonnet' | 'opus' | 'haiku' | 'inherit')

### Common Tool Combinations
- **Read-only**: `['Read', 'Grep', 'Glob']`
- **Test execution**: `['Bash', 'Read', 'Grep']`
- **Code modification**: `['Read', 'Edit', 'Write', 'Grep', 'Glob']`

### Invocation Methods
1. **Automatic**: SDK invokes based on task matching
2. **Explicit**: User requests specific agent

### Alternative: Filesystem-based
```markdown
<!-- .claude/agents/security-reviewer.md -->
---
name: Security Reviewer
description: Specializes in finding security vulnerabilities
tools: [Read, Grep, Glob]
model: sonnet
---

You are a security expert. When reviewing code:
- Identify injection vulnerabilities
- Check authentication/authorization
- Look for XSS, CSRF risks
- Verify input validation
```

---

## Slash Commands

### Overview
Control sessions with `/` commands.

### Built-in Commands
- `/compact` - Summarize conversation history
- `/clear` - Clear conversation history
- `/help` - Show command assistance

### Discovering Commands
```typescript
// Available in system init message
for await (const message of query({ prompt: "Hello" })) {
  if (message.type === "system_init") {
    const commands = message.slash_commands;
  }
}
```

### Custom Commands

#### Creating Commands
Create markdown files in:
- Project: `.claude/commands/`
- User: `~/.claude/commands/`

```markdown
<!-- .claude/commands/run-tests.md -->
---
name: Run Tests
description: Execute test suite with coverage report
---

# Run Tests

Execute the full test suite and provide coverage report.

## Usage
/run-tests [pattern]

## Arguments
- pattern: Optional test file pattern to match

## Execution
!npm test -- --coverage
```

#### Command Features
- YAML frontmatter for configuration
- Arguments with `$1`, `$2` placeholders
- Bash execution with `!` prefix
- File references with `@` prefix
- Subdirectories for organization

#### Example Commands
- Code review commands
- Test runners
- Git operations
- Security checks
- Refactoring tasks

---

## Skills

### Overview
Specialized capabilities packaged as `SKILL.md` files. Claude autonomously invokes them.

### Key Characteristics
- **Filesystem-based**: Created as `SKILL.md` in directories
- **Must configure**: `settingSources` to load from filesystem
- **Automatically discovered**: Metadata at startup
- **Model-invoked**: Claude chooses when to use
- **Enable via allowed_tools**: Add `"Skill"` to enabled tools

### Using Skills

```python
options = ClaudeAgentOptions(
    setting_sources=["user", "project"],
    allowed_tools=["Skill", "Read", "Write", "Bash"]
)
```

### Skill Locations
- **Project**: `.claude/skills/` (git-shared)
- **User**: `~/.claude/skills/` (personal, cross-project)
- **Plugin**: Bundled with installed plugins

### Creating Skills

```
.claude/skills/processing-pdfs/
└── SKILL.md
```

```markdown
<!-- SKILL.md -->
---
name: Process PDFs
description: Extract and analyze content from PDF documents
tools: [Read, Bash, Write]
---

# PDF Processing

Extract text, images, and metadata from PDF files.

## When to Use
- Analyzing research papers
- Extracting data from reports
- Converting PDFs to structured data

## Capabilities
- Text extraction
- Image extraction
- Metadata analysis
- OCR for scanned documents
```

### Important Notes
- **SDK doesn't provide programmatic APIs** for Skills (must be filesystem)
- **Default: no filesystem settings loaded** - must configure `settingSources`
- **allowed-tools in SKILL.md only works in CLI**, not SDK
- **In SDK, use main `allowedTools`** to control tool access

### Testing & Discovery
- Ask: "What Skills are available?"
- Test by asking questions matching descriptions
- Claude invokes relevant Skills automatically

### Troubleshooting

#### Skills Not Found
- Configure `settingSources: ["user", "project"]` (MOST COMMON)
- Verify `cwd` points to directory with `.claude/skills/`
- Check for `SKILL.md` files in correct paths

#### Skill Not Used
- Confirm "Skill" in `allowedTools`
- Check description specificity and keywords

---

## Todo Tracking

### Overview
Structured task management with lifecycle tracking.

### Todo Lifecycle
1. **Created**: `pending` when task identified
2. **Activated**: `in_progress` when work begins
3. **Completed**: Finished successfully
4. **Removed**: When all tasks in group complete

### When Used
- Complex multi-step tasks (3+ actions)
- User-provided task lists
- Non-trivial operations
- Explicit requests for organization

### Monitoring Todos

```typescript
class TodoTracker {
  todos: Map<string, Todo> = new Map();

  updateTodos(messages: any[]) {
    for (const message of messages) {
      if (message.type === "task") {
        this.todos.set(message.id, message);
        this.display();
      }
    }
  }

  display() {
    console.log("\n=== TODO LIST ===");
    for (const [id, todo] of this.todos) {
      console.log(`${todo.status.toUpperCase()}: ${todo.content}`);
    }
    console.log("================\n");
  }
}

const tracker = new TodoTracker();

for await (const message of query({ prompt: "Build a full-stack app" })) {
  tracker.updateTodos([message]);
}
```

### Example Use Cases
- Building applications (setup, development, testing, deployment)
- Code reviews (analyze, refactor, optimize, document)
- Data analysis (clean, process, visualize, report)
- Research (explore, analyze, synthesize, present)

---

## Quick Reference

### Basic Query Structure
```typescript
const result = await query({
  prompt: "Your instruction here",
  options: {
    // Optional configurations
    systemPrompt: { type: "preset", preset: "claude_code" },
    settingSources: ["project", "user"],
    permissions: { mode: "default" },
    allowedTools: ["Read", "Write", "Bash"],
    resume: "session-id",
    forkSession: false,
    agents: { /* subagent definitions */ },
    mcpServers: { /* custom tools */ }
  }
});
```

### Essential Tools
- **Read**: Read file contents
- **Write**: Create new files
- **Edit**: Modify file sections
- **Bash**: Execute commands
- **Grep**: Search file contents
- **Glob**: Find files by pattern
- **WebFetch**: Fetch web content
- **WebSearch**: Search the web
- **Skill**: Use filesystem skills
- **Search**: Search and replace

### Configuration Checklist
- [ ] Set `workingDirectory` or `cwd`
- [ ] Configure `settingSources` for CLAUDE.md, skills, commands
- [ ] Set `allowedTools` (start minimal, expand as needed)
- [ ] Configure `permissions` mode based on use case
- [ ] Set up `mcpServers` for custom tools
- [ ] Define `agents` for specialized subagents
- [ ] Handle `resume` for session continuity
- [ ] Monitor `maxTurns` to prevent loops

### Security Best Practices
- Use container-based sandboxing
- Start with restrictive `allowedTools`
- Use `default` permissions mode
- Avoid `bypassPermissions` in production
- Validate all inputs in custom tools
- Log all tool usage
- Regular security audits
- Isolate sensitive operations

### Common Patterns

#### 1. Code Development
```typescript
const result = await query({
  prompt: "Build a React component",
  options: {
    settingSources: ["project"],
    permissions: { mode: "acceptEdits" },
    allowedTools: ["Read", "Write", "Edit", "Bash", "Grep"]
  }
});
```

#### 2. Research Agent
```typescript
const result = await query({
  prompt: "Research machine learning trends",
  options: {
    allowedTools: ["WebSearch", "WebFetch", "Read", "Write"],
    agents: {
      "web-researcher": {
        description: "Searches and analyzes web content",
        tools: ["WebSearch", "WebFetch"],
        model: "haiku"
      }
    }
  }
});
```

#### 3. Code Reviewer
```typescript
const result = await query({
  prompt: "Review this codebase",
  options: {
    allowedTools: ["Read", "Grep", "Glob", "Bash"],
    agents: {
      "security-reviewer": {
        description: "Finds security vulnerabilities",
        tools: ["Read", "Grep"],
        model: "sonnet"
      },
      "style-checker": {
        description: "Checks code style and standards",
        tools: ["Read", "Bash"],
        model: "haiku"
      }
    }
  }
});
```

---

## Resources

### Documentation
- [Claude Agent SDK Overview](/en/api/agent-sdk/overview)
- [TypeScript SDK Reference](/en/api/agent-sdk/typescript)
- [Python SDK Reference](/en/api/agent-sdk/python)
- [Claude Code Guide](/en/docs/claude-code)

### Related Features
- [Custom Tools Guide](/en/api/agent-sdk/custom-tools)
- [MCP Protocol](/en/api/agent-sdk/mcp)
- [Subagents](/en/api/agent-sdk/subagents)
- [Agent Skills](/en/api/agent-sdk/skills)
- [Slash Commands](/en/api/agent-sdk/slash-commands)

### Community
- [Claude Code GitHub](https://github.com/anthropics/claude-code)
- [Claude Code Discord](https://discord.gg/claude)
- [Anthropic Blog](https://www.anthropic.com/news)

---

## Appendix: Complete Examples

### Example 1: Full-Featured Application
```typescript
import { query } from "@anthropic-ai/claude-agent-sdk";

const result = await query({
  prompt: "Create a todo application with React and Node.js",
  options: {
    systemPrompt: {
      type: "preset",
      preset: "claude_code",
      append: "Use TypeScript and modern best practices."
    },
    settingSources: ["project"],
    permissions: { mode: "acceptEdits" },
    allowedTools: [
      "Read", "Write", "Edit", "Bash", "Grep", "Glob",
      "Skill", "WebSearch"
    ],
    agents: {
      "frontend-dev": {
        description: "Builds React components",
        tools: ["Read", "Write", "Edit"],
        model: "sonnet"
      },
      "backend-dev": {
        description: "Builds Node.js APIs",
        tools: ["Read", "Write", "Bash"],
        model: "sonnet"
      },
      "tester": {
        description: "Writes and runs tests",
        tools: ["Read", "Write", "Bash"],
        model: "haiku"
      }
    },
    mcpServers: {
      "database": {
        // Custom database tool
      }
    }
  }
});
```

### Example 2: Research Agent with Persistence
```typescript
// Session 1: Initial research
const session1 = await query({
  prompt: "Research the history of AI",
  options: {
    allowedTools: ["WebSearch", "WebFetch", "Read", "Write"]
  }
});

// Session 2: Resume and continue
const session2 = await query({
  prompt: "Continue with modern AI developments",
  options: {
    resume: session1.sessionId,
    allowedTools: ["WebSearch", "WebFetch", "Read", "Write"]
  }
});

// Session 3: Fork to explore different angle
const forkedSession = await query({
  prompt: "Explore AI ethics from a different perspective",
  options: {
    resume: session1.sessionId,
    forkSession: true
  }
});
```

### Example 3: Collaborative Multi-Agent System
```typescript
const result = await query({
  prompt: "Build and deploy a web application",
  options: {
    agents: {
      "architect": {
        description: "Designs system architecture",
        tools: ["Read", "Write", "Bash"],
        model: "opus"
      },
      "frontend": {
        description: "Implements UI components",
        tools: ["Read", "Write", "Edit", "Grep"],
        model: "sonnet"
      },
      "backend": {
        description: "Implements API services",
        tools: ["Read", "Write", "Bash", "Grep"],
        model: "sonnet"
      },
      "tester": {
        description: "Writes and runs tests",
        tools: ["Read", "Write", "Bash"],
        model: "haiku"
      },
      "deployer": {
        description: "Handles deployment and DevOps",
        tools: ["Bash", "Read"],
        model: "haiku"
      }
    }
  }
});
```

---

**End of Comprehensive Guide**
