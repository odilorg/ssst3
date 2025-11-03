# Blog Comment Moderation - Filament Admin Implementation Plan

## Overview
Create a complete Filament admin resource for moderating blog comments with intuitive UI, bulk actions, and comprehensive filtering.

---

## Phase 1: Create Filament Resource

### 1.1 Generate Resource
```bash
php artisan make:filament-resource BlogComment --generate
```

### 1.2 Configure Resource Class
**File**: `app/Filament/Resources/BlogCommentResource.php`

**Key Configurations**:
- Model: `App\Models\BlogComment`
- Navigation Icon: `heroicon-o-chat-bubble-left-right`
- Navigation Label: "Comments"
- Navigation Group: "Blog Management"
- Default Sort: `created_at DESC`
- Records Per Page: 25

---

## Phase 2: Table Configuration

### 2.1 Table Columns

**Display Columns**:
1. **ID** - `TextColumn::make('id')`
   - Sortable
   - Searchable
   - Width: 60px

2. **Author Name** - `TextColumn::make('author_name')`
   - Searchable
   - Sortable
   - Bold text
   - Limit: 30 chars

3. **Comment Preview** - `TextColumn::make('comment')`
   - Limit: 80 chars
   - Wrap text
   - Searchable
   - Color: gray

4. **Blog Post** - `TextColumn::make('post.title')`
   - Searchable
   - Limit: 40 chars
   - Link to edit post
   - Icon: document

5. **Status Badge** - `BadgeColumn::make('status')`
   - Color mapping:
     - `pending`: warning (yellow)
     - `approved`: success (green)
     - `spam`: danger (red)
     - `trash`: secondary (gray)
   - Icons for each status

6. **Spam Score** - `TextColumn::make('spam_score')`
   - Sortable
   - Badge with color gradient:
     - 0-30: green
     - 31-69: yellow
     - 70-100: red
   - Suffix: "/100"

7. **Flags** - `TextColumn::make('flag_count')`
   - Sortable
   - Badge if > 0
   - Color: red when >= 3
   - Icon: flag

8. **Reply To** - `TextColumn::make('parent.author_name')`
   - Toggleable
   - Shows parent comment author
   - Prefix icon: reply arrow

9. **Email** - `TextColumn::make('author_email')`
   - Toggleable (hidden by default)
   - Searchable
   - Copyable on click

10. **IP Address** - `TextColumn::make('author_ip')`
    - Toggleable (hidden by default)
    - Monospace font
    - Copyable

11. **Created At** - `TextColumn::make('created_at')`
    - Sortable
    - Date format: "M d, Y H:i"
    - Relative time tooltip (diffForHumans)

12. **Approved At** - `TextColumn::make('approved_at')`
    - Toggleable (hidden by default)
    - Date format
    - Null state: "Not approved"

### 2.2 Table Filters

**Filter Set**:

1. **Status Filter** - `SelectFilter::make('status')`
   - Options: All, Pending, Approved, Spam, Trash
   - Default: "All"
   - Multiple selection allowed

2. **Spam Score Range** - `Filter::make('spam_score')`
   - Range slider: 0-100
   - Presets: Low (0-30), Medium (31-69), High (70-100)

3. **Has Flags** - `TernaryFilter::make('has_flags')`
   - Options: All, Flagged, Not Flagged
   - Query: `flag_count > 0`

4. **Is Reply** - `TernaryFilter::make('is_reply')`
   - Options: All, Top-Level, Replies
   - Query: `parent_id IS NULL/NOT NULL`

5. **Blog Post Filter** - `SelectFilter::make('blog_post_id')`
   - Relationship: `post`
   - Searchable
   - Show post titles

6. **Date Range** - `Filter::make('created_at')`
   - Date range picker
   - Presets: Today, This Week, This Month, Last 30 Days

### 2.3 Table Actions

**Row Actions**:

1. **Quick Approve** - Green button
   - Icon: check-circle
   - Visible only if status != 'approved'
   - Updates status to 'approved'
   - Sets approved_at to now()
   - Shows success notification
   - Refreshes table

2. **Quick Reject** - Red button
   - Icon: x-circle
   - Visible only if status != 'spam'
   - Updates status to 'spam'
   - Shows notification
   - Refreshes table

3. **View Details** - Modal action
   - Shows full comment
   - Shows all metadata
   - Shows blog post context
   - Shows reply thread if applicable
   - Shows Gravatar avatar

4. **Edit** - Standard edit action
   - Opens edit form

5. **Delete** - Confirmation required
   - Soft delete if applicable
   - Hard delete with double confirmation

### 2.4 Bulk Actions

1. **Bulk Approve**
   - Icon: check-circle
   - Color: success
   - Updates all selected to 'approved'
   - Confirmation required
   - Shows count in notification

2. **Bulk Mark as Spam**
   - Icon: shield-exclamation
   - Color: danger
   - Updates all selected to 'spam'
   - Confirmation required

3. **Bulk Move to Trash**
   - Icon: trash
   - Color: warning
   - Updates all selected to 'trash'
   - Confirmation required

4. **Bulk Delete**
   - Icon: trash
   - Color: danger
   - Permanent deletion
   - Double confirmation required
   - Disabled if > 50 selected

---

## Phase 3: Form Configuration

### 3.1 Form Schema

**Sections**:

#### Section 1: Comment Information
```php
Section::make('Comment Information')
    ->schema([
        Textarea::make('comment')
            ->label('Comment Text')
            ->required()
            ->rows(6)
            ->maxLength(2000)
            ->columnSpanFull(),

        Grid::make(2)->schema([
            TextInput::make('author_name')
                ->required()
                ->maxLength(100),

            TextInput::make('author_email')
                ->email()
                ->required()
                ->maxLength(150),

            TextInput::make('author_website')
                ->url()
                ->maxLength(200),

            TextInput::make('author_ip')
                ->disabled()
                ->dehydrated(false),
        ]),
    ])
```

#### Section 2: Blog Post Context
```php
Section::make('Blog Post')
    ->schema([
        Select::make('blog_post_id')
            ->label('Blog Post')
            ->relationship('post', 'title')
            ->searchable()
            ->required()
            ->preload(),

        Select::make('parent_id')
            ->label('Reply To Comment')
            ->relationship('parent', 'author_name')
            ->searchable()
            ->placeholder('Top-level comment')
            ->helperText('Select a parent comment to make this a reply'),
    ])
```

#### Section 3: Moderation
```php
Section::make('Moderation')
    ->schema([
        Select::make('status')
            ->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'spam' => 'Spam',
                'trash' => 'Trash',
            ])
            ->required()
            ->default('pending')
            ->reactive(),

        TextInput::make('spam_score')
            ->numeric()
            ->minValue(0)
            ->maxValue(100)
            ->default(0)
            ->suffix('/100')
            ->helperText('Spam likelihood score (0-100)'),

        TextInput::make('flag_count')
            ->numeric()
            ->minValue(0)
            ->default(0)
            ->helperText('Number of times flagged by users'),

        DateTimePicker::make('approved_at')
            ->label('Approved At')
            ->disabled()
            ->visible(fn ($get) => $get('status') === 'approved'),
    ])
```

#### Section 4: Additional Info (Collapsed by default)
```php
Section::make('Additional Information')
    ->collapsed()
    ->schema([
        TextInput::make('author_user_agent')
            ->disabled()
            ->columnSpanFull(),

        Grid::make(2)->schema([
            Placeholder::make('created_at')
                ->label('Created At')
                ->content(fn ($record) => $record?->created_at?->format('M d, Y H:i:s')),

            Placeholder::make('updated_at')
                ->label('Updated At')
                ->content(fn ($record) => $record?->updated_at?->format('M d, Y H:i:s')),
        ]),
    ])
```

---

## Phase 4: Custom Actions & Widgets

### 4.1 Header Actions

**Create Comment Button**:
```php
CreateAction::make()
    ->label('New Comment')
    ->icon('heroicon-o-plus-circle')
```

**Export Action**:
```php
ExportAction::make()
    ->label('Export Comments')
    ->icon('heroicon-o-arrow-down-tray')
    ->exports([
        ExportColumn::make('id'),
        ExportColumn::make('author_name'),
        ExportColumn::make('author_email'),
        ExportColumn::make('comment'),
        ExportColumn::make('status'),
        ExportColumn::make('spam_score'),
        ExportColumn::make('created_at'),
    ])
```

### 4.2 Stats Overview Widget

**File**: `app/Filament/Widgets/CommentStatsWidget.php`

**Stats Cards**:
1. **Total Comments**
   - Count of all comments
   - Icon: chat-bubble-left-right
   - Color: primary

2. **Pending Review**
   - Count of pending comments
   - Icon: clock
   - Color: warning
   - Click to filter pending

3. **Approved**
   - Count of approved comments
   - Icon: check-circle
   - Color: success

4. **Spam Detected**
   - Count of spam comments
   - Icon: shield-exclamation
   - Color: danger

5. **Avg Spam Score**
   - Average spam score across all comments
   - Format: "X/100"
   - Icon: chart-bar

6. **Flagged by Users**
   - Count of comments with flag_count > 0
   - Icon: flag
   - Color: orange

### 4.3 Recent Comments Widget

**File**: `app/Filament/Widgets/RecentCommentsWidget.php`

**Features**:
- Table widget showing last 10 comments
- Columns: Author, Comment Preview, Status, Post, Created
- Quick actions: Approve, Spam
- Click to view full comment

---

## Phase 5: Relations & Pages

### 5.1 Relation Managers

#### BlogPostResource - Comments Relation
```php
public static function getRelations(): array
{
    return [
        RelationManagers\CommentsRelationManager::class,
    ];
}
```

**CommentsRelationManager Features**:
- Shows all comments for the blog post
- Inline editing
- Quick approve/spam actions
- Nested replies visible
- Add new comment inline

### 5.2 Custom View Page

**File**: `app/Filament/Resources/BlogCommentResource/Pages/ViewComment.php`

**Sections**:
1. **Comment Details** - Full comment with formatting
2. **Author Information** - Name, email, website, Gravatar
3. **Blog Post Context** - Link to post, post title, excerpt
4. **Moderation History** - Timeline of status changes
5. **Reply Thread** - Show parent comment and all replies
6. **Actions Sidebar** - Quick moderate buttons

---

## Phase 6: Notifications

### 6.1 Admin Notifications

**Trigger Notifications On**:
1. New comment submitted (pending review)
2. Comment flagged by user (3+ flags)
3. High spam score detected (>70)

**Notification Format**:
```php
Notification::make()
    ->title('New Comment Pending Review')
    ->body("{$comment->author_name} commented on \"{$comment->post->title}\"")
    ->icon('heroicon-o-chat-bubble-left-right')
    ->iconColor('warning')
    ->actions([
        Action::make('approve')
            ->button()
            ->url(BlogCommentResource::getUrl('edit', ['record' => $comment])),
        Action::make('view')
            ->button()
            ->url(BlogCommentResource::getUrl('view', ['record' => $comment])),
    ])
    ->send();
```

### 6.2 Email Notifications (Optional)

**Send Email When**:
1. Comment approved → Notify commenter
2. Comment marked as spam → Admin notification
3. Multiple flags received → Admin alert

---

## Phase 7: Policies & Permissions

### 7.1 Create Policy

```bash
php artisan make:policy BlogCommentPolicy --model=BlogComment
```

**Permissions**:
- `viewAny`: See comments list
- `view`: View individual comment
- `create`: Create new comment (admin only)
- `update`: Edit comment content
- `delete`: Delete comments
- `moderate`: Approve/reject/spam comments
- `bulk_moderate`: Bulk actions

### 7.2 Register Policy

**File**: `app/Providers/AuthServiceProvider.php`
```php
protected $policies = [
    BlogComment::class => BlogCommentPolicy::class,
];
```

---

## Phase 8: Navigation & Branding

### 8.1 Navigation Setup

**Navigation Badge**: Show pending count
```php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::where('status', 'pending')->count();
}

public static function getNavigationBadgeColor(): string|array|null
{
    return static::getModel()::where('status', 'pending')->count() > 0
        ? 'warning'
        : 'success';
}
```

### 8.2 Global Search

**Enable global search**:
```php
public static function getGloballySearchableAttributes(): array
{
    return ['author_name', 'author_email', 'comment', 'post.title'];
}

public static function getGlobalSearchResultTitle(Model $record): string
{
    return $record->author_name;
}

public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Post' => $record->post->title,
        'Comment' => Str::limit($record->comment, 50),
    ];
}
```

---

## Phase 9: Advanced Features

### 9.1 Comment Reply Thread View

**Custom Infolist** to show nested replies:
```php
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('Comment Thread')
                ->schema([
                    RepeatableEntry::make('replies')
                        ->schema([
                            TextEntry::make('author_name'),
                            TextEntry::make('comment'),
                            TextEntry::make('created_at'),
                        ]),
                ]),
        ]);
}
```

### 9.2 Bulk Spam Detection

**Custom Bulk Action**:
```php
BulkAction::make('recalculate_spam_scores')
    ->label('Recalculate Spam Scores')
    ->icon('heroicon-o-calculator')
    ->requiresConfirmation()
    ->action(function (Collection $records) {
        $controller = new CommentController();
        foreach ($records as $comment) {
            $spamScore = $controller->calculateSpamScore(
                $comment->comment,
                $comment->author_website
            );
            $comment->update(['spam_score' => $spamScore]);
        }
    })
```

### 9.3 Auto-Moderate Action

**Scheduled Task** to auto-moderate based on rules:
```php
Schedule::call(function () {
    // Auto-approve comments from trusted emails
    BlogComment::where('status', 'pending')
        ->whereIn('author_email', TrustedEmailList::pluck('email'))
        ->where('spam_score', '<', 30)
        ->update(['status' => 'approved', 'approved_at' => now()]);

    // Auto-spam high score comments
    BlogComment::where('status', 'pending')
        ->where('spam_score', '>=', 80)
        ->update(['status' => 'spam']);
})->daily();
```

---

## Phase 10: Testing Checklist

### 10.1 Functional Tests

- [ ] View comments list
- [ ] Filter by status
- [ ] Filter by spam score
- [ ] Search by author name
- [ ] Search by comment text
- [ ] Search by blog post
- [ ] Approve single comment
- [ ] Reject single comment
- [ ] Bulk approve multiple comments
- [ ] Bulk spam multiple comments
- [ ] View comment details modal
- [ ] Edit comment content
- [ ] Delete comment (with confirmation)
- [ ] See nested replies
- [ ] Create new comment manually
- [ ] Export comments to CSV
- [ ] Navigation badge shows correct pending count
- [ ] Global search finds comments
- [ ] Stats widget shows accurate counts

### 10.2 Permission Tests

- [ ] Non-admin cannot access comments
- [ ] Moderator can approve/reject
- [ ] Moderator cannot delete
- [ ] Admin can perform all actions

### 10.3 UI/UX Tests

- [ ] Table is responsive
- [ ] Filters work correctly
- [ ] Actions show appropriate confirmations
- [ ] Notifications appear on actions
- [ ] Loading states visible
- [ ] Empty state shown when no comments

---

## Implementation Steps

### Step-by-Step Execution Order:

1. ✅ Generate Filament resource
2. ✅ Configure table columns
3. ✅ Add table filters
4. ✅ Create table actions
5. ✅ Add bulk actions
6. ✅ Configure form schema
7. ✅ Create stats widget
8. ✅ Add recent comments widget
9. ✅ Setup navigation badge
10. ✅ Create policy and permissions
11. ✅ Add global search
12. ✅ Create custom view page
13. ✅ Add relation manager to BlogPost
14. ✅ Configure notifications
15. ✅ Test all functionality

---

## Estimated Time: 3-4 hours

**Breakdown**:
- Resource setup: 30 min
- Table configuration: 45 min
- Form and actions: 45 min
- Widgets: 30 min
- Policies: 20 min
- Testing: 30 min
- Polish and refinement: 30 min

---

## Dependencies

**Required Packages** (already installed):
- `filament/filament: ^4.0`
- `filament/tables: ^4.0`
- `filament/forms: ^4.0`
- `filament/notifications: ^4.0`

**No additional packages needed** ✅

---

## Notes

- All moderation actions should clear relevant caches
- Email notifications are optional (can be added later)
- Consider adding comment moderation queue for high-traffic sites
- Implement IP-based blocking if needed
- Add trusted commenter whitelist feature
- Consider adding comment edit history tracking

---

## End Result

A fully-featured comment moderation system with:
- ✅ Intuitive table view with all necessary columns
- ✅ Powerful filtering and search
- ✅ Quick action buttons for fast moderation
- ✅ Bulk actions for efficient management
- ✅ Stats dashboard for overview
- ✅ Proper permissions and policies
- ✅ Notifications for important events
- ✅ Export capabilities
- ✅ Nested reply support
- ✅ Spam score visualization
- ✅ Flag management
