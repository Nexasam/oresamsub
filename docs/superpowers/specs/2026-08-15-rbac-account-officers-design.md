# RBAC and Account Officer Design

## Goal

Introduce safe multi-role staff authorization and use it to support accountable customer ownership, weighted assignment, officer follow-ups, email notifications, and performance reporting.

## Compatibility

`users.role_id` remains the legacy primary role. Customer accounts continue using the `User` primary role and existing role-name checks continue operating during migration. A new `role_user` pivot adds zero or more supplementary roles. Effective permissions are the union of the primary role and supplementary roles.

The user with email `adebsholey4real@gmail.com` is the protected Super Admin. This identity has every permission and is initially the only identity allowed to manage roles, assign staff roles, configure account officers, or execute customer allocation.

## Authorization model

Permissions use stable capability names such as `users.view`, `transactions.refund`, and `followups.log_call`. Roles receive many permissions and users receive many roles. Permission middleware protects routes; policies or scoped controller queries enforce record-level ownership. Sidebar visibility reflects permissions but is never the security boundary.

The first secured modules are roles/access, users/staff management, customer retention, and account officers. Other admin modules remain compatible with the legacy `admin` middleware and will migrate incrementally to the permission catalogue.

## Initial permission catalogue

- Dashboard: `dashboard.view`
- Users: `users.view`, `users.create`, `users.update`, `users.deactivate`, `users.impersonate`
- Roles/access: `roles.view`, `roles.manage`, `staff.roles.assign`
- Transactions: `transactions.view`, `transactions.update_status`, `transactions.refund`, `transactions.manual_process`
- Funding: `funding.view`, `funding.approve`, `funding.reject`
- Wallets: `wallets.view`, `wallets.credit`, `wallets.debit`, `wallet_logs.view`
- Commissions: `commissions.view`, `commissions.manage`
- Retention: `followups.view_all`, `followups.view_assigned`, `followups.log_call`, `followups.view_performance`
- Account officers: `officers.view`, `officers.manage`, `officers.allocate_customers`, `officers.redistribute_customers`
- Catalogue/operations: product, category, plan, pricing, network, automation, reseller, and bulk-plan capabilities using view/create/update/delete plus sensitive pricing and credential capabilities
- Campaigns/finance: coupon, promotion, bonus, affiliate-finance, profit, announcement, translation, settings, and system-log capabilities

## Account officers

An account officer is an existing user holding the Account Officer role and an active officer profile. Officers log in normally. They can see only their assigned customers, record call outcomes and feedback, schedule follow-ups, and view their own performance. Users with `followups.view_all` can see every portfolio.

A customer has at most one current officer. Assignment history stores the customer, officer, assignment batch, start time, end time, and assigning administrator. Call logs retain both the assigned officer context and actual caller.

## Weighted allocation

The protected management screen lists active officers and accepts an allocation percentage for each. Active weights must total exactly 100%. Preview shows the number of currently unassigned customers and the projected allocation per officer.

Execution assigns only unassigned customer accounts. Weighted round-robin distribution keeps results close to configured percentages. Execution runs inside a transaction with locking, creates a versioned assignment batch, and is safe against duplicate submission.

Deactivation prevents new assignments but does not silently move existing customers. A protected Redistribute action ends only that officer's active assignments, makes those customers unassigned, and distributes them using the current active weights after preview and confirmation.

## Email and audit

Officers already have system access, so assignment emails summarize newly assigned customers and link to their dashboard. Email delivery is queued after the database transaction; a mail failure never rolls back assignments. Allocation batches, role changes, permission changes, officer activation, and redistribution record the initiating user and timestamp.

## Performance

Officer reporting supports a selected date range and shows portfolio size, contacted customers, overdue follow-ups, active customers, stale customers, suddenly inactive customers, reactivated customers, contact rate, and reactivation rate. Attribution uses assignment ownership periods so reassignment does not rewrite historical performance.

## Error handling and security

- Unauthorized requests receive 403 responses; hiding menu items is supplementary only.
- Officers cannot access another officer's customers by changing URLs or form identifiers.
- The protected Super Admin identity cannot lose its effective access through role edits.
- Allocation rejects missing active officers, negative weights, totals other than 100%, stale previews, and concurrent duplicate execution.
- Deleting roles or officers with active dependencies is blocked or converted to deactivation.

## Testing

Feature tests cover primary-role compatibility, multiple-role permission union, Super Admin bypass, permission middleware, officer portfolio isolation, call-log ownership, weighted allocation, unassigned-only behavior, deactivated-officer redistribution, assignment history, queued email notifications, concurrency/idempotency protections, and period-correct performance attribution.
