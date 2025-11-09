# Departmental AI Architecture: Single vs Multiple Agents

## The Three Approaches

### 1. Single Main Agent + Department Subagents (Recommended for most cases)

```
Main Agent (Orchestrator)
  ├── Dev Team Subagents
  │   ├── Frontend Developer
  │   ├── Backend Developer
  │   ├── DevOps Engineer
  │   └── QA Tester
  ├── Marketing Team Subagents
  │   ├── Content Writer
  │   ├── SEO Specialist
  │   ├── Social Media Manager
  │   └── Brand Strategist
  └── Accounting Team Subagents
      ├── Bookkeeper
      ├── Tax Specialist
      ├── Financial Analyst
      └── Auditor
```

**Best for**:
- ✅ Projects where departments collaborate closely
- ✅ Limited administrative overhead desired
- ✅ Shared context between departments
- ✅ Single conversation/session
- ✅ Coordinated releases/deployments

**Example**:
```typescript
const result = await query({
  prompt: "Build and launch a new SaaS product with marketing campaign",
  options: {
    agents: {
      // Development
      "frontend-dev": { tools: ["Read", "Write"], model: "sonnet" },
      "backend-dev": { tools: ["Read", "Write", "Bash"], model: "sonnet" },
      "devops": { tools: ["Bash", "Read"], model: "haiku" },

      // Marketing
      "content-writer": { tools: ["Read", "Write"], model: "sonnet" },
      "seo-specialist": { tools: ["WebSearch", "Read", "Write"], model: "haiku" },
      "social-manager": { tools: ["Read", "Write"], model: "haiku" },

      // Accounting
      "financial-analyst": { tools: ["Read", "Write", "Bash"], model: "sonnet" },
      "bookkeeper": { tools: ["Read", "Write"], model: "haiku" }
    }
  }
});
```

---

### 2. Multiple Main Agents (Peer-to-Peer)

```
Agent 1: Dev Department (Specialized for development)
  ├── Frontend Subagent
  ├── Backend Subagent
  └── DevOps Subagent

Agent 2: Marketing Department (Specialized for marketing)
  ├── Content Subagent
  ├── SEO Subagent
  └── Social Subagent

Agent 3: Accounting Department (Specialized for finance)
  ├── Bookkeeping Subagent
  ├── Tax Subagent
  └── Analysis Subagent
```

**Best for**:
- ✅ Truly independent work streams
- ✅ Different priority levels
- ✅ Deep specialization per department
- ✅ Team-specific workflows
- ✅ Parallel, independent development

**Example**:
```typescript
// Dev Department Agent
const devAgent = await query({
  prompt: "Build the technical infrastructure",
  options: {
    agents: {
      "frontend": { tools: ["Read", "Write"] },
      "backend": { tools: ["Read", "Write", "Bash"] },
      "devops": { tools: ["Bash", "Read"] }
    }
  }
});

// Marketing Department Agent (Separate session)
const marketingAgent = await query({
  prompt: "Create marketing materials and campaign",
  options: {
    agents: {
      "content": { tools: ["Read", "Write"] },
      "seo": { tools: ["WebSearch", "Read"] },
      "social": { tools: ["Read", "Write"] }
    }
  }
});

// Accounting Department Agent (Separate session)
const accountingAgent = await query({
  prompt: "Set up financial tracking and reporting",
  options: {
    agents: {
      "bookkeeper": { tools: ["Read", "Write"] },
      "analyst": { tools: ["Read", "Write", "Bash"] }
    }
  }
});
```

---

### 3. Hybrid (Department Agents + Top-Level Coordinator)

```
Top-Level Coordinator Agent
  ├── Dev Department Agent
  │   ├── Frontend Subagent
  │   ├── Backend Subagent
  │   └── DevOps Subagent
  ├── Marketing Department Agent
  │   ├── Content Subagent
  │   ├── SEO Subagent
  │   └── Social Subagent
  └── Accounting Department Agent
      ├── Bookkeeper Subagent
      ├── Tax Subagent
      └── Analyst Subagent
```

**Best for**:
- ✅ Large organizations
- ✅ Complex interdependencies
- ✅ Multiple simultaneous projects
- ✅ Team autonomy with central oversight

---

## Comparison Table

| Criteria | Single Main Agent | Multiple Main Agents | Hybrid |
|----------|-------------------|----------------------|--------|
| **Complexity** | Low | Medium | High |
| **Coordination** | Easy (central) | Hard (manual) | Medium (automated) |
| **Parallelism** | Good | Excellent | Excellent |
| **Context Sharing** | Excellent | Difficult | Good |
| **Scaling** | Moderate | Easy | Easy |
| **Specialization** | Good | Excellent | Excellent |
| **Setup Time** | 5 minutes | 15 minutes | 30 minutes |
| **Maintenance** | Easy | Medium | Hard |
| **Inter-Department Work** | Excellent | Poor | Good |
| **Independent Work** | Good | Excellent | Good |

---

## Decision Framework

### Choose **Single Main Agent + Subagents** if:

✅ **Projects require collaboration** between departments
Example: "Build and market a product" - dev needs to know marketing timeline

✅ **Shared context matters**
Example: Marketing needs to know what features are being built

✅ **Sequential or dependent tasks**
Example: Dev builds → Marketing creates materials → Accounting sets up tracking

✅ **Limited resources/time for setup**
You want to get started quickly

✅ **Single project/initiative**
One product, one campaign, one goal

**Code Example**:
```typescript
const result = await query({
  prompt: "Launch new mobile app with marketing and financial tracking",
  options: {
    agents: {
      // Dev (2 agents)
      "mobile-dev": { tools: ["Read", "Write"], model: "sonnet" },
      "backend-dev": { tools: ["Read", "Write", "Bash"], model: "sonnet" },

      // Marketing (2 agents)
      "content-writer": { tools: ["Read", "Write"], model: "sonnet" },
      "seo-manager": { tools: ["WebSearch", "Read"], model: "haiku" },

      // Accounting (2 agents)
      "financial-setup": { tools: ["Read", "Write"], model: "sonnet" },
      "reporting": { tools: ["Read", "Bash"], model: "haiku" }
    }
  }
});
```

---

### Choose **Multiple Main Agents** if:

✅ **Departments work independently**
Example: Dev working on Product A while Marketing runs campaign for Product B

✅ **Different priority/timeline requirements**
Example: Dev has 3-month project, Marketing has urgent campaign

✅ **Need deep specialization**
Example: Each department has complex, unique workflows

✅ **Multiple concurrent projects**
Example: Dev team on Project X, Marketing on Project Y, Accounting on Project Z

✅ **Department-specific tools/resources**
Example: Dev needs GitHub, Marketing needs social media APIs, Accounting needs QuickBooks

**Code Example**:
```typescript
// Start 3 separate conversations
const [dev, marketing, accounting] = await Promise.all([
  // Dev - Working on infrastructure
  query({ prompt: "Build backend API", options: { agents: { "backend": {}, "frontend": {} } } }),

  // Marketing - Creating campaign
  query({ prompt: "Design social media campaign", options: { agents: { "content": {}, "design": {} } } }),

  // Accounting - Setting up systems
  query({ prompt: "Implement expense tracking", options: { agents: { "bookkeeper": {}, "analyst": {} } } })
]);
```

---

### Choose **Hybrid** if:

✅ **Large organization with multiple projects**
Example: 5 products, 3 teams each with 5-10 specialists

✅ **Complex dependencies but need autonomy**
Example: Dev needs to know marketing deadlines, but teams work independently

✅ **Multiple project managers**
Example: Each department has its own PM coordinating subagents

---

## Real-World Scenarios

### Scenario 1: Startup Building MVP
**Challenge**: Limited resources, need speed, departments collaborate
**Solution**: **Single Main Agent + Subagents**

```typescript
// One conversation, all teams coordinated
const mvp = await query({
  prompt: "Build MVP for our idea: Create todo app with marketing campaign",
  options: {
    agents: {
      // Dev (2 specialists)
      "fullstack-dev": { tools: ["Read", "Write", "Bash"], model: "sonnet" },
      "tester": { tools: ["Read", "Bash"], model: "haiku" },

      // Marketing (2 specialists)
      "content-creator": { tools: ["Read", "Write"], model: "sonnet" },
      "social-media": { tools: ["Read", "Write"], model: "haiku" },

      // Accounting (2 specialists)
      "bookkeeper": { tools: ["Read", "Write"], model: "haiku" },
      "financial-planner": { tools: ["Read", "Write"], model: "sonnet" }
    }
  }
});
```

**Why**: Single conversation, easy coordination, shared context, fast execution

---

### Scenario 2: Enterprise with Multiple Products
**Challenge**: 3 products, each needs dev/marketing/accounting, different timelines
**Solution**: **Multiple Main Agents**

```typescript
// Product 1 Team
const product1Dev = query({ prompt: "Build Product 1", options: { agents: { "dev": {}, "qa": {} } } });
const product1Marketing = query({ prompt: "Market Product 1", options: { agents: { "content": {}, "ads": {} } } });
const product1Accounting = query({ prompt: "Track Product 1 finances", options: { agents: { "finance": {} } } });

// Product 2 Team
const product2Dev = query({ prompt: "Build Product 2", options: { agents: { "dev": {}, "qa": {} } } });
const product2Marketing = query({ prompt: "Market Product 2", options: { agents: { "content": {}, "ads": {} } } });
// ... and so on

await Promise.all([product1Dev, product1Marketing, /* ... */]);
```

**Why**: Each product team is independent, can work at different speeds, specialized for their product

---

### Scenario 3: Marketing Agency with Multiple Clients
**Challenge**: 5 clients, each needs content/design/SEO/account management
**Solution**: **Multiple Main Agents (one per client)**

```typescript
// Client A Team
const clientA_content = query({ prompt: "Write content for Client A", options: { /* content agents */ } });
const clientA_design = query({ prompt: "Create designs for Client A", options: { /* design agents */ } });
const clientA_seo = query({ prompt: "Optimize SEO for Client A", options: { /* SEO agents */ } });
const clientA_account = query({ prompt: "Manage Client A account", options: { /* account agents */ } });

// Client B Team
const clientB_content = query({ prompt: "Write content for Client B", /* ... */ });
// ... repeat for each client

// All work in parallel
await Promise.all([clientA_content, clientA_design, clientA_seo, clientA_account, clientB_content, /* ... */]);
```

**Why**: Each client is independent, can work in parallel, specialized per client needs

---

## Implementation Examples

### Option 1: Single Main Agent (Simplest)

```typescript
// One agent orchestrating everything
const result = await query({
  prompt: "Complete project: Build website, create marketing materials, set up accounting",
  options: {
    agents: {
      // Dev
      "frontend": { tools: ["Read", "Write"], model: "sonnet" },
      "backend": { tools: ["Read", "Write", "Bash"], model: "sonnet" },
      "tester": { tools: ["Bash", "Read"], model: "haiku" },

      // Marketing
      "copywriter": { tools: ["Read", "Write"], model: "sonnet" },
      "designer": { tools: ["Read", "Write"], model: "sonnet" },
      "seo": { tools: ["WebSearch", "Read"], model: "haiku" },

      // Accounting
      "bookkeeper": { tools: ["Read", "Write"], model: "haiku" },
      "analyst": { tools: ["Read", "Write", "Bash"], model: "sonnet" }
    }
  }
});
```

**Total**: 1 main query, 8 subagents working in parallel

---

### Option 2: Multiple Main Agents (Most Scalable)

```typescript
// Dev Department - Dedicated session
const devDepartment = await query({
  prompt: "Handle all development tasks for our organization",
  options: {
    agents: {
      "frontend": { tools: ["Read", "Write"], model: "sonnet" },
      "backend": { tools: ["Read", "Write", "Bash"], model: "sonnet" },
      "devops": { tools: ["Bash", "Read"], model: "haiku" },
      "qa": { tools: ["Read", "Bash"], model: "haiku" }
    }
  }
});

// Marketing Department - Separate session
const marketingDepartment = await query({
  prompt: "Handle all marketing tasks for our organization",
  options: {
    agents: {
      "content": { tools: ["Read", "Write"], model: "sonnet" },
      "design": { tools: ["Read", "Write"], model: "sonnet" },
      "social": { tools: ["Read", "Write"], model: "haiku" },
      "seo": { tools: ["WebSearch", "Read"], model: "haiku" },
      "analytics": { tools: ["Read", "Bash"], model: "sonnet" }
    }
  }
});

// Accounting Department - Separate session
const accountingDepartment = await query({
  prompt: "Handle all accounting tasks for our organization",
  options: {
    agents: {
      "bookkeeper": { tools: ["Read", "Write"], model: "haiku" },
      "tax": { tools: ["Read", "Write"], model: "sonnet" },
      "payroll": { tools: ["Read", "Write", "Bash"], model: "haiku" },
      "financial-analyst": { tools: ["Read", "Write", "Bash"], model: "sonnet" }
    }
  }
});

// Run all departments in parallel
await Promise.all([devDepartment, marketingDepartment, accountingDepartment]);
```

**Total**: 3 main queries, each with subagents, all running in parallel

---

### Option 3: Hybrid (Most Complex, Most Powerful)

```typescript
// Top-level orchestrator
const coordinator = await query({
  prompt: "Coordinate multiple department projects",
  options: {
    agents: {
      "dev-dept": {
        description: "Handles all development work",
        tools: ["Read", "Write", "Bash"],
        model: "sonnet"
      },
      "marketing-dept": {
        description: "Handles all marketing work",
        tools: ["Read", "Write", "WebSearch"],
        model: "sonnet"
      },
      "accounting-dept": {
        description: "Handles all accounting work",
        tools: ["Read", "Write", "Bash"],
        model: "sonnet"
      }
    }
  }
});
```

**Note**: The department agents themselves would have their own subagents internally.

---

## Best Practices

### For Single Main Agent + Subagents:

1. **Limit subagents per department** (2-3 max for clarity)
2. **Use clear, specific descriptions** for each subagent
3. **Restrict tools appropriately** per role
4. **Choose models based on complexity** (Haiku for simple, Sonnet/Opus for complex)
5. **Group related subagents** (e.g., all dev together, all marketing together)

```typescript
// Good: 6 subagents, clearly organized
agents: {
  // Dev (2)
  "frontend": { description: "Builds React components", tools: ["Read", "Write"] },
  "backend": { description: "Creates Node.js APIs", tools: ["Read", "Write", "Bash"] },

  // Marketing (2)
  "writer": { description: "Creates marketing copy", tools: ["Read", "Write"] },
  "designer": { description: "Creates visual assets", tools: ["Read", "Write"] },

  // Accounting (2)
  "bookkeeper": { description: "Records transactions", tools: ["Read", "Write"] },
  "analyst": { description: "Creates financial reports", tools: ["Read", "Write", "Bash"] }
}

// Avoid: 20+ subagents = chaos
```

---

### For Multiple Main Agents:

1. **One agent per department** (don't over-segment)
2. **Each agent has 2-4 subagents** (managable complexity)
3. **Use session management** (resume when needed)
4. **Document agent specializations** (what each does)
5. **Monitor resource usage** (multiple agents = more tokens)

```typescript
// Good: 3 main agents, each with 3 subagents
const devAgent = query({
  prompt: "You are the development department head",
  options: {
    agents: { "frontend": {}, "backend": {}, "devops": {} }
  }
});

const marketingAgent = query({
  prompt: "You are the marketing department head",
  options: {
    agents: { "content": {}, "design": {}, "seo": {} }
  }
});

const accountingAgent = query({
  prompt: "You are the accounting department head",
  options: {
    agents: { "bookkeeping": {}, "tax": {}, "payroll": {} }
  }
});
```

---

## Recommendation by Company Size

### **Solo Founder / Small Startup (1-5 people)**
→ **Single Main Agent + Subagents**
- Simple setup
- Easy coordination
- Low cost
- Fast execution

**Example**: "Build and launch MVP"

---

### **Small-Medium Company (5-50 people)**
→ **Single Main Agent + Subagents** (if projects are related)
→ **Multiple Main Agents** (if independent projects)

**Example**: "Build Product X while marketing Product Y"

---

### **Medium-Large Company (50-500 people)**
→ **Multiple Main Agents** (one per major project/department)
→ **Hybrid** (if complex interdependencies)

**Example**: 3 products, each with dev/marketing/accounting teams

---

### **Enterprise (500+ people)**
→ **Hybrid** (department agents + subagents)
→ **Multiple Main Agents** (one per division)

**Example**: Multiple business units, each with their own AI agent

---

## Cost Considerations

### Single Main Agent
- **Pros**: Lower token usage (shared context)
- **Cons**: Single model for everything
- **Cost**: Moderate

### Multiple Main Agents
- **Pros**: Can use cheaper models per department
- **Cons**: More token usage (multiple conversations)
- **Cost**: Higher (3x conversations = 3x tokens)

**Example**:
- Single agent with 8 subagents: ~5,000 tokens
- 3 agents with 8 subagents total: ~10,000 tokens (parallel + coordination)

**Optimization tip**:
```typescript
// Use cheaper models for simple tasks
"data-entry": { model: "haiku" },  // Fast, cheap
"strategy-planner": { model: "opus" }  // Capable, more expensive
```

---

## Summary

### Quick Decision Tree

```
Do departments work on same project?
├─ YES → Single Main Agent + Subagents
│   └─ Example: "Build app + market it + track finances"
│
└─ NO → Multiple Main Agents
    ├─ Small scale (3-5 agents) → Multiple Main Agents
    └─ Large scale (10+ agents) → Hybrid

Need coordination between departments?
├─ YES → Single Main Agent or Hybrid
│
└─ NO → Multiple Main Agents
```

### Final Recommendation

**Start with Single Main Agent + Subagents** for most use cases:
- Easier to understand and debug
- Lower complexity
- Good performance
- Easy to scale later

**Upgrade to Multiple Main Agents when**:
- Departments work on truly independent projects
- Need different priority levels
- Want to specialize deeply
- Have complex workflows per department

**Use Hybrid only when**:
- Large organization
- Multiple simultaneous projects
- Complex interdependencies
- Have dedicated AI coordinators

---

**Bottom line**: For dev/marketing/accounting departments, I'd recommend **starting with a Single Main Agent + Subagents** unless you have specific reasons to separate them. It's simpler, more coordinated, and easier to manage. You can always evolve to multiple agents later as your needs grow.
