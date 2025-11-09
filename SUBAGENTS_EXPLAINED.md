# Subagents in Claude Agent SDK - Detailed Explanation

## What Are Subagents?

**Subagents are specialized AI agents that are orchestrated by a main agent.** Think of them as a team of experts where the main agent acts as a project manager, delegating specific tasks to the right specialist.

Each subagent:
- Has its own **specialized instructions** (system prompt)
- Maintains **separate context** from the main agent
- Can be **restricted to specific tools**
- Runs in **parallel** with other subagents
- Has a **specific role and expertise**

---

## Why Use Subagents?

### 1. **Context Management**
**Problem**: One agent trying to do everything gets overwhelmed with too much context.

**Solution**: Subagents keep context separate. Each agent only sees what it needs.

**Example**: A `research-assistant` subagent can explore files without cluttering the main conversation with its analysis details.

```typescript
agents: {
  "researcher": {
    description: "Explores and analyzes code",
    tools: ["Read", "Grep"],
    // This agent's context is separate from main agent
  }
}
```

### 2. **Parallelization**
**Problem**: Sequential task execution is slow.

**Solution**: Multiple subagents run concurrently, speeding up workflows.

**Example**: During code review, these can run **simultaneously**:
- `security-scanner` - Checks for vulnerabilities
- `style-checker` - Verifies coding standards
- `test-coverage` - Analyzes test quality

```typescript
agents: {
  "security-scanner": { tools: ["Read", "Grep"] },
  "style-checker": { tools: ["Read", "Bash"] },
  "test-coverage": { tools: ["Read", "Bash"] }
}
// All three run at the same time!
```

### 3. **Specialization**
**Problem**: Generic agents make generic mistakes.

**Solution**: Each subagent has **tailored instructions** for its specific role.

```typescript
agents: {
  "security-expert": {
    description: "Specializes in security vulnerabilities",
    prompt: `You are a security expert. Always check for:
    - SQL injection risks
    - XSS vulnerabilities
    - Authentication flaws
    - Authorization bypasses
    Focus ONLY on security issues.`,
    tools: ["Read", "Grep"]
  }
}
```

### 4. **Tool Restrictions**
**Problem**: Giving one agent all tools is dangerous.

**Solution**: Subagents get **only the tools they need**.

```typescript
agents: {
  "reader": {
    description: "Reads and analyzes files",
    tools: ["Read"],  // Only read - can't modify files!
    model: "haiku"
  },
  "editor": {
    description: "Modifies files safely",
    tools: ["Read", "Write", "Edit"],  // Modify files
    model: "sonnet"
  }
}
```

---

## How to Create Subagents

### Method 1: Programmatically (Recommended)

Define subagents directly in your query options.

#### TypeScript Example
```typescript
import { query } from "@anthropic-ai/claude-agent-sdk";

const result = await query({
  prompt: "Review this authentication module for security issues",
  options: {
    agents: {
      "code-reviewer": {
        description: "Expert code review specialist for security and quality",
        prompt: `You are a security-focused code reviewer.

        When reviewing code:
        - Identify security vulnerabilities (injection, XSS, CSRF)
        - Check authentication and authorization
        - Look for data exposure risks
        - Verify input validation
        - Suggest specific improvements

        Be thorough but concise.`,
        tools: ["Read", "Grep", "Glob"],
        model: "sonnet"
      },
      "style-checker": {
        description: "Checks code style and standards",
        prompt: "Verify code follows project conventions. Check naming, formatting, and best practices.",
        tools: ["Read", "Bash"],
        model: "haiku"
      },
      "test-coverage": {
        description: "Analyzes test coverage and quality",
        prompt: "Analyze test coverage. Identify missing tests and suggest improvements.",
        tools: ["Read", "Bash"],
        model: "haiku"
      }
    }
  }
});
```

#### Python Example
```python
from claude_agent_sdk import query

result = await query(
    prompt="Build a REST API with authentication",
    options={
        "agents": {
            "api-designer": {
                "description": "Designs API endpoints and schemas",
                "prompt": "Design clean, RESTful API endpoints. Use proper HTTP methods, status codes, and documentation.",
                "tools": ["Read", "Write", "Grep"],
                "model": "sonnet"
            },
            "auth-specialist": {
                "description": "Implements secure authentication",
                "prompt": "Implement secure authentication using JWT, bcrypt, and proper session management.",
                "tools": ["Read", "Write", "Bash"],
                "model": "sonnet"
            }
        }
    }
)
```

### Method 2: Filesystem-Based

Create markdown files with YAML frontmatter.

#### Directory Structure
```
.claude/agents/
├── security-reviewer.md
├── style-checker.md
└── test-analyzer.md
```

#### Example: security-reviewer.md
```markdown
---
name: Security Reviewer
description: Specializes in finding security vulnerabilities in code
tools: [Read, Grep, Glob]
model: sonnet
---

# Security Reviewer

You are a cybersecurity expert specializing in application security.

## Expertise
- **Vulnerability Discovery**: Find injection flaws, XSS, CSRF, authentication bypass
- **Security Patterns**: Identify insecure coding patterns
- **Best Practices**: Recommend secure alternatives

## Focus Areas
1. Input validation and sanitization
2. Authentication and authorization
3. Data encryption (at rest and in transit)
4. Error handling (information disclosure)
5. Session management
6. Security headers

## Response Format
- List each issue found
- Severity level (Critical, High, Medium, Low)
- Description of the vulnerability
- Example of the issue
- Recommended fix
- References (CWE, OWASP)
```

**Note**: Filesystem-based subagents work, but **programmatic definition is recommended** for better type safety and control.

---

## AgentDefinition Configuration

Each subagent is defined by an `AgentDefinition` object with these properties:

### 1. description (Required)
**Natural language description of when to use this agent.**

```typescript
"security-reviewer": {
  description: "Specializes in finding security vulnerabilities",
  // ...
}
```

The main agent uses this to decide which subagent to invoke.

### 2. prompt (Required)
**The subagent's system prompt defining its role and behavior.**

```typescript
prompt: `You are an expert code reviewer.
Focus on: security, performance, and maintainability.
Be thorough but concise.`
```

This is the subagent's "personality" and instructions.

### 3. tools (Optional)
**Array of tool names the subagent can use.**

```typescript
tools: ["Read", "Grep", "Glob"]  // Read-only tools
```

**Important**:
- If omitted, subagent inherits **all** available tools
- List specific tools to restrict capabilities
- Main agent controls overall `allowedTools` list

### 4. model (Optional)
**Model override for this specific agent.**

```typescript
model: "sonnet"  // Use Claude Sonnet
```

**Options**:
- `"sonnet"` - Best balance of capability and speed
- `"opus"` - Most capable model
- `"haiku"` - Fastest, good for simple tasks
- `"inherit"` - Use same model as main agent (default)

---

## Common Tool Combinations

### Read-Only Agents
```typescript
tools: ["Read", "Grep", "Glob"]
```
**Use for**: Analysis, research, code review

### Test Execution
```typescript
tools: ["Bash", "Read", "Grep"]
```
**Use for**: Running tests, coverage analysis

### Code Modification
```typescript
tools: ["Read", "Edit", "Write", "Grep", "Glob"]
```
**Use for**: Refactoring, feature development

### Full Access
```typescript
tools: ["Read", "Write", "Edit", "Bash", "Grep", "Glob", "WebSearch", "WebFetch"]
```
**Use for**: General development tasks

### Safe Development
```typescript
"frontend": {
  tools: ["Read", "Write", "Edit"]  // Can modify files
},
"database": {
  tools: ["Read", "Bash"]  // Can run SQL, can't edit code
}
```

---

## How Subagents Work

### 1. **Creation**
Main agent receives task → Analyzes what subagents are needed → Invokes them

### 2. **Execution**
Subagents run in **parallel** → Process their assigned tasks → Return results

### 3. **Integration**
Main agent receives all results → Synthesizes into final response → Presents to user

### Example Workflow

**User Prompt**: "Build a todo app with tests"

**Main Agent** decides to invoke:
- `frontend-dev` - Build React components
- `backend-dev` - Create API endpoints
- `tester` - Write and run tests

**Parallel Execution**:
```
Time -->
  |--- frontend-dev works on UI
  |--- backend-dev works on API
  |--- tester works on tests
  |------------------------
  | All complete at same time!
```

**Result**: Main agent combines all outputs into final response.

---

## Invocation Methods

### 1. **Automatic Invocation** (Default)
The SDK automatically invokes appropriate subagents based on task matching.

```typescript
// User says:
"Review this authentication module"

// Main agent sees:
// - Task requires security expertise
// - Subagent "security-reviewer" description matches
// → Automatically invokes security-reviewer
```

### 2. **Explicit Invocation**
User can request specific subagents in their prompt.

```typescript
// User says:
"Use the security-reviewer subagent to check this code"

// Or:
"Invoke the style-checker agent"

// Main agent will invoke those specific subagents
```

---

## Complete Example: Full-Stack App Development

```typescript
const result = await query({
  prompt: "Build and deploy a full-stack todo application",
  options: {
    agents: {
      "architect": {
        description: "Designs system architecture and database schema",
        prompt: `You are a software architect. Design scalable, maintainable systems.

        Consider:
        - Database design (normalization, indexes)
        - API structure (RESTful, versioning)
        - Frontend architecture (components, state management)
        - Security (authentication, authorization, data protection)
        - Performance (caching, lazy loading, optimization)

        Provide architecture diagrams and justifications.`,
        tools: ["Read", "Write", "Bash", "Grep"],
        model: "opus"
      },
      "frontend-developer": {
        description: "Builds React components and UI",
        prompt: `You are a frontend specialist. Create modern, accessible React applications.

        Standards:
        - Use TypeScript and strict mode
        - Functional components with hooks
        - Responsive design (mobile-first)
        - Accessible (ARIA labels, keyboard navigation)
        - State management (Context or Redux)
        - Performance optimization

        Create clean, well-documented code.`,
        tools: ["Read", "Write", "Edit", "Grep", "Glob"],
        model: "sonnet"
      },
      "backend-developer": {
        description: "Implements API services and business logic",
        prompt: `You are a backend specialist. Build robust, scalable APIs.

        Requirements:
        - RESTful API design
        - Proper HTTP status codes
        - Input validation and sanitization
        - Error handling
        - Authentication (JWT)
        - Database optimization
        - API documentation

        Use best practices and security guidelines.`,
        tools: ["Read", "Write", "Edit", "Bash", "Grep"],
        model: "sonnet"
      },
      "database-specialist": {
        description: "Optimizes database design and queries",
        prompt: `You are a database expert. Design efficient database schemas and queries.

        Focus on:
        - Normalization (3rd normal form)
        - Indexing strategy
        - Query optimization
        - Data integrity constraints
        - Backup and recovery plans

        Explain your design choices.`,
        tools: ["Read", "Write", "Bash"],
        model: "sonnet"
      },
      "test-engineer": {
        description: "Writes comprehensive tests",
        prompt: `You are a testing expert. Create thorough test suites.

        Test Types:
        - Unit tests (components, functions)
        - Integration tests (API endpoints)
        - E2E tests (user workflows)
        - Security tests (authentication, authorization)

        Coverage:
        - Aim for >80% code coverage
        - Test edge cases
        - Mock external services
        - Use proper test data

        Provide test reports.`,
        tools: ["Read", "Write", "Bash", "Grep"],
        model: "haiku"
      },
      "devops-engineer": {
        description: "Handles deployment and DevOps",
        prompt: `You are a DevOps specialist. Automate deployment and infrastructure.

        Responsibilities:
        - Docker containerization
        - CI/CD pipeline setup
        - Environment configuration
        - Monitoring and logging
        - Security scanning
        - Rollback procedures

        Document deployment process.`,
        tools: ["Bash", "Read", "Write"],
        model: "haiku"
      }
    }
  }
});
```

**Result**: 6 specialized agents work together, each focusing on their area of expertise, all in parallel, producing a complete application.

---

## Benefits Summary

| Benefit | How Subagents Help | Example |
|---------|-------------------|---------|
| **Context Management** | Separate context per agent | Research doesn't clutter main chat |
| **Parallelization** | Concurrent execution | 3 agents work simultaneously |
| **Specialization** | Tailored instructions | Security expert finds vulnerabilities |
| **Tool Restrictions** | Limited capabilities | Read-only agent can't delete files |
| **Cost Efficiency** | Use cheaper models for simple tasks | Haiku for reading, Sonnet for writing |
| **Reliability** | Focused agents make fewer mistakes | Style-checker catches formatting issues |
| **Scalability** | Easy to add new agents | Add "performance-optimization" agent |
| **Team Collaboration** | Each agent is a specialist | Like having a team of experts |

---

## Best Practices

### 1. **Write Clear Descriptions**
```typescript
"good-description": {
  description: "Analyzes code security for OWASP Top 10 vulnerabilities",  // Specific
  // vs
  "bad-description": {
  description: "Checks code"  // Too vague
}
```

### 2. **Restrict Tools Appropriately**
```typescript
"reader": {
  tools: ["Read"],  // Only what they need
  // Don't give Write if they only need to read
}
```

### 3. **Use Appropriate Models**
```typescript
"simple-analyzer": {
  model: "haiku",  // Fast for simple tasks
  // vs
  "complex-planner": {
  model: "opus"  // Capable for complex reasoning
}
```

### 4. **Keep Prompts Focused**
```typescript
"security-reviewer": {
  prompt: "Focus ONLY on security. Don't comment on style or performance.",  // Clear scope
}
```

### 5. **Test and Iterate**
Start simple → Add complexity → Adjust based on results

---

## Common Use Cases

### 1. **Code Review Team**
```typescript
agents: {
  "security-auditor": { tools: ["Read", "Grep"] },
  "style-police": { tools: ["Read", "Bash"] },
  "test-coverage": { tools: ["Read", "Bash"] }
}
```

### 2. **Research Team**
```typescript
agents: {
  "web-researcher": { tools: ["WebSearch", "WebFetch"] },
  "data-analyzer": { tools: ["Read", "Bash", "Grep"] },
  "report-writer": { tools: ["Read", "Write"] }
}
```

### 3. **Development Team**
```typescript
agents: {
  "frontend": { tools: ["Read", "Write", "Edit"] },
  "backend": { tools: ["Read", "Write", "Bash"] },
  "tester": { tools: ["Read", "Write", "Bash"] }
}
```

### 4. **Content Team**
```typescript
agents: {
  "researcher": { tools: ["WebSearch", "Read", "Write"] },
  "writer": { tools: ["Read", "Write"] },
  "editor": { tools: ["Read", "Write", "Grep"] }
}
```

---

## Advanced Patterns

### Pattern 1: **Pipeline**
One agent's output feeds into the next.

```typescript
agents: {
  "file-analyzer": {
    description: "Analyzes file structure and dependencies",
    tools: ["Read", "Grep"]
  },
  "dependency-updater": {
    description: "Updates dependencies based on analysis",
    tools: ["Read", "Write", "Bash"],
    // Uses output from file-analyzer
  }
}
```

### Pattern 2: **Specialized Expertise**
Same task, different perspectives.

```typescript
agents: {
  "security-perspective": {
    description: "Reviews from security angle",
    prompt: "Focus on vulnerabilities and risks",
    tools: ["Read", "Grep"]
  },
  "performance-perspective": {
    description: "Reviews from performance angle",
    prompt: "Focus on speed and optimization",
    tools: ["Read", "Grep"]
  },
  "maintainability-perspective": {
    description: "Reviews from maintainability angle",
    prompt: "Focus on code quality and readability",
    tools: ["Read", "Grep"]
  }
}
```

### Pattern 3: **Hierarchical**
Main agent coordinates sub-subagents.

```typescript
agents: {
  "project-manager": {
    description: "Coordinates the project",
    tools: ["Read", "Write", "Grep"],
    model: "opus"  // Most capable for coordination
  },
  "team-leader": {
    description: "Leads development team",
    tools: ["Read", "Write", "Bash"],
    // May invoke its own subagents
  }
}
```

---

## Troubleshooting

### Problem: Subagent Not Being Invoked

**Check**:
1. Is the description specific and clear?
2. Does the main agent's `allowedTools` include the subagent's tools?
3. Is the prompt too vague?

**Solution**:
```typescript
"better-description": {
  description: "Reviews JavaScript code for security vulnerabilities using OWASP guidelines",  // More specific
  // ...
}
```

### Problem: Subagent Making Mistakes

**Check**:
1. Is the prompt clear and specific?
2. Are the right tools restricted?
3. Is the model appropriate?

**Solution**:
```typescript
"improved-agent": {
  prompt: `You are an expert in Node.js security.

  Specifically check for:
  1. SQL injection (use parameterized queries)
  2. XSS (sanitize user input)
  3. Authentication (verify tokens)

  Ignore: styling, formatting, performance

  Return findings in this format:
  - Issue: [description]
  - Severity: [Critical/High/Medium/Low]
  - Location: [file:line]
  - Fix: [recommendation]`,
  // ...
}
```

### Problem: Context Getting Mixed

**Solution**: Subagents already have separate context. If issues persist:
- Make prompts more explicit about scope
- Use tool restrictions
- Consider creating more specific subagents

---

## Comparison: Subagents vs Skills vs Custom Tools

| Feature | Subagents | Skills | Custom Tools |
|---------|-----------|--------|--------------|
| **Purpose** | Task execution | Context enhancement | Extend capabilities |
| **Invocation** | Automatic/explicit | Automatic | Explicit only |
| **Context** | Separate per agent | Shared with main | Shared |
| **Scope** | Single task | Context provider | Function/operation |
| **Definition** | Programmatic or filesystem | Filesystem | Programmatic |
| **Tools** | Configurable | Main agent's tools | Configurable |
| **Use Case** | Parallel execution | Domain knowledge | API/service integration |

**When to use**:
- **Subagents**: Multiple perspectives, parallel work, specialization
- **Skills**: Domain expertise, knowledge enhancement
- **Custom Tools**: API integration, external services

---

## Summary

**Subagents are the key to building sophisticated, multi-perspective AI systems.** They enable:

1. ✅ **Parallel execution** for speed
2. ✅ **Specialization** for quality
3. ✅ **Context separation** for clarity
4. ✅ **Tool control** for security
5. ✅ **Cost optimization** with appropriate models

Think of them as your **AI development team** - each with their own expertise, working together to solve complex problems efficiently and accurately.

**Start simple** with 2-3 subagents, then **scale up** as you understand your workflow better.
