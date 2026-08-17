# Production-Ready Laravel + Filament Architecture

## Complete Development Roles, Responsibilities, Design Patterns, Security & Performance Standards

---

# 1. Purpose

This document defines the complete engineering roles and responsibilities required to build the system as a **production-ready Laravel + Filament application**.

The implementation must prioritize:

* Clean Architecture
* SOLID principles
* Separation of Concerns
* Maintainability
* Scalability
* Security
* Performance
* Testability
* Reusability
* High availability
* Database efficiency
* API security
* Filament administration
* DDoS and abuse protection

The system must not be implemented as a simple CRUD application.

---

# 2. Core Architecture Rules

The application must follow this responsibility hierarchy:

```text
Filament (Admin UI) / Web Controllers (Blade Views + Ajax)
        ↓
Form Requests / Validation
        ↓
Services / Domain Actions
        ↓
Eloquent Models
        ↓
Database (MySQL)
```

Supporting infrastructure:

```text
Providers
Policies
Events
Listeners
Jobs
Notifications
Observers
DTOs
Enums
Traits
Exceptions
Cache
Queues
Logging
```

The same business logic must be reusable from:

```text
REST API
Filament
Console Commands
Queue Jobs
Scheduled Tasks
Internal Services
```

---

# 3. Senior Backend Architect Role

The architecture must be designed according to enterprise-level backend standards.

Responsibilities:

* Define application architecture.
* Define module boundaries.
* Define business domains.
* Define service boundaries.
* Define repository architecture.
* Define dependency direction.
* Define security architecture.
* Define scalability strategy.
* Define database access strategy.
* Define caching strategy.
* Define queue architecture.
* Define API architecture.
* Define Filament architecture.
* Define testing strategy.
* Prevent architectural coupling.
* Review implementation against architectural standards.

The architecture must avoid unnecessary complexity while remaining scalable.

---

# 4. Software Engineering Principles

The implementation must follow:

## SOLID

### Single Responsibility Principle

Each class must have one clear responsibility.

Bad:

```text
OrderController
    ├── Validation
    ├── Database Queries
    ├── Payment
    ├── Notification
    ├── Inventory
    └── Reporting
```

Good:

```text
OrderController
        ↓
OrderService
        ↓
Repositories
        ↓
Integrations
        ↓
Events / Jobs
```

### Open/Closed Principle

The system should be extendable without continuously modifying existing core logic.

### Liskov Substitution Principle

Implementations must correctly follow their contracts.

### Interface Segregation Principle

Do not create massive interfaces containing unrelated methods.

### Dependency Inversion Principle

High-level business logic should depend on abstractions where appropriate.

---

# 5. Controller Role

Controllers are HTTP entry points.

Responsibilities:

* Receive requests.
* Call authorization.
* Receive validated data.
* Call Services.
* Return API Resources/responses.
* Handle HTTP-specific concerns.

Controllers must NOT contain:

* Complex business logic.
* Large database queries.
* Payment processing.
* Inventory workflows.
* Notification workflows.
* Complex calculations.
* External API implementation.

Example:

```php
public function store(StoreOrderRequest $request)
{
    $order = $this->orderService->create(
        $request->validated()
    );

    return new OrderResource($order);
}
```

---

# 6. Form Request Role

Form Requests are responsible for request validation and request-level authorization.

Responsibilities:

* Validate fields.
* Validate formats.
* Validate relationships.
* Validate business-input constraints.
* Authorize the request when appropriate.

Do not duplicate the same validation rules throughout Controllers and Services.

---

# 7. Service Layer Role

Services contain the application's business logic.

Responsibilities:

* Business workflows.
* Business rules.
* Transactions.
* Calculations.
* Multiple repository coordination.
* External service coordination.
* Events.
* Jobs.
* Notifications.
* State transitions.

Example:

```text
OrderService
    ↓
Validate Business Rules
    ↓
Check Inventory
    ↓
Calculate Total
    ↓
Create Order
    ↓
Update Inventory
    ↓
Create Payment
    ↓
Dispatch Event
```

Services are the primary business-logic layer.

---

# 8. Repository Role

Repositories are responsible for data access.

Responsibilities:

* Eloquent queries.
* Query Builder operations.
* Filtering.
* Searching.
* Pagination.
* Relationship loading.
* Database-specific operations.

Repositories must NOT contain:

* Payment workflows.
* Notifications.
* Authorization.
* Business workflows.
* Complex business rules.

---

# 9. Repository Contracts

Repository interfaces should be placed in:

```text
app/Repositories/Contracts/
```

Example:

```php
interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;

    public function delete(Order $order): bool;
}
```

Implementations:

```text
app/Repositories/Eloquent/
```

---

# 10. Service Provider Role

Service Providers are responsible for dependency registration and application bootstrapping.

Responsibilities:

* Bind interfaces.
* Register implementations.
* Register Singleton services where justified.
* Register application configuration.
* Register package integrations.
* Register custom application services.

Example:

```php
$this->app->bind(
    OrderRepositoryInterface::class,
    OrderRepository::class
);
```

---

# 11. Singleton Pattern Role

Singleton must NOT be used by default.

Use Singleton only when one shared application instance is genuinely appropriate.

Possible use cases:

* API client manager.
* Configuration manager.
* Stateless infrastructure manager.
* Shared application context.
* Specialized cache manager.

Use Laravel's container:

```php
$this->app->singleton(
    ApiClientManager::class,
    fn ($app) => new ApiClientManager()
);
```

Do not manually implement static Singleton patterns unless there is a strong architectural reason.

---

# 12. Trait Role

Traits must provide small reusable behaviors.

Good examples:

```text
HasUuid
HasStatus
HasActivityLog
HasTenant
HasTranslations
```

Traits must NOT contain:

* Large workflows.
* Complete business processes.
* Multiple unrelated responsibilities.
* Hidden database workflows.

Avoid:

```text
OrderBusinessLogicTrait
ApplicationLogicTrait
EverythingTrait
```

---

# 13. Model Role

Models represent database entities and relationships.

Responsibilities:

* Relationships.
* Casts.
* Accessors/mutators where appropriate.
* Scopes.
* Basic model-level behavior.
* Attribute configuration.

Models should not become massive business-logic containers.

Complex business operations belong in Services.

---

# 14. Filament Role

Filament is the primary administration interface.

Filament must handle:

* Admin dashboards.
* Resources.
* Forms.
* Tables.
* Filters.
* Actions.
* Widgets.
* Notifications.
* Admin navigation.
* Admin workflows.
* Resource authorization integration.

Filament must NOT become the business-logic layer.

---

# 15. Filament Resource Role

Filament Resources are responsible for admin UI configuration.

Responsibilities:

* Form schema.
* Table schema.
* Filters.
* Actions.
* Relations.
* UI-specific behavior.

Complex operations must call Services.

Example:

```php
Action::make('approve')
    ->action(function ($record) {
        app(OrderService::class)->approve($record);
    });
```

The same `OrderService` must be reusable by API and other entry points.

---

# 16. Filament Security Role

Never treat hidden buttons as authorization.

This is NOT sufficient:

```text
Hide Delete Button
```

Correct:

```text
Filament Action
      ↓
Policy
      ↓
Permission
      ↓
Service
      ↓
Business Rule
```

Every sensitive Filament operation must be authorized server-side.

---

# 17. Policy Role

Policies are responsible for authorization.

Examples:

```text
UserPolicy
OrderPolicy
ProductPolicy
PaymentPolicy
```

Policies should answer:

```text
Can this user perform this action?
```

They should not implement the complete business workflow.

---

# 18. Permission & Role Architecture

Use a clear RBAC architecture:

```text
User
  ↓
Role
  ↓
Permissions
  ↓
Policies
  ↓
Resource / Action
```

Permissions must be enforced on the backend.

Do not trust:

* Frontend permissions.
* Hidden UI buttons.
* LocalStorage roles.
* Client-side authorization.

---

# 19. Events Role

Events represent important business occurrences.

Examples:

```text
OrderCreated
OrderApproved
PaymentCompleted
UserRegistered
InvoiceGenerated
```

Events should be used when multiple independent listeners may react to one business event.

---

# 20. Listener Role

Listeners handle secondary actions triggered by Events.

Example:

```text
OrderCreated
    ├── SendNotification
    ├── SendEmail
    ├── UpdateAnalytics
    └── CreateAuditLog
```

Listeners prevent Services from becoming unnecessarily large.

---

# 21. Job & Queue Role

Jobs are responsible for asynchronous or expensive operations.

Use Jobs for:

* Emails.
* Notifications.
* Reports.
* Exports.
* Imports.
* External synchronization.
* Large data processing.
* Heavy calculations.

Example:

```text
Request
 ↓
Service
 ↓
Dispatch Job
 ↓
Redis Queue
 ↓
Worker
```

---

# 22. Queue Reliability Role

Jobs must have:

* Timeout.
* Retry limits.
* Backoff.
* Failure handling.
* Idempotency where required.
* Monitoring.

Never allow unlimited retries.

---

# 23. DTO Role

DTOs should be used when complex data crosses application boundaries.

Examples:

```text
CreateOrderDTO
UpdateOrderDTO
PaymentDTO
UserRegistrationDTO
```

DTOs should make service inputs explicit and predictable.

---

# 24. Enum Role

Use PHP Enums for fixed states.

Examples:

```text
OrderStatus
PaymentStatus
UserStatus
SubscriptionStatus
```

Avoid repeating magic strings throughout the application.

---

# 25. Strategy Pattern Role

Use Strategy when multiple implementations solve the same problem.

Example:

```text
PaymentGatewayInterface
       ↓
 ┌─────┴─────┐
 ↓           ↓
Paymob      Fawry
```

The business layer should depend on the interface.

---

# 26. Factory Pattern Role

Use Factory when object creation depends on runtime configuration.

Example:

```text
PaymentGatewayFactory
        ↓
Payment Gateway
        ↓
Paymob / Fawry / Other
```

Do not introduce factories when simple dependency injection is sufficient.

---

# 27. External Integration Role

External services must be isolated.

Recommended:

```text
app/Services/Integrations/

Payment/
SMS/
Email/
Shipping/
ExternalAPI/
```

Use interfaces when multiple providers are possible.

External APIs must not be directly implemented inside Controllers or Models.

---

# 28. Transaction Role

Use transactions for multi-step operations.

Example:

```text
BEGIN TRANSACTION
    ↓
Create Order
    ↓
Create Items
    ↓
Update Inventory
    ↓
Create Payment
    ↓
COMMIT
```

If any critical operation fails:

```text
ROLLBACK
```

---

# 29. Database Query Security Role

Database operations must be optimized and protected.

The implementation must prevent:

* SQL injection.
* N+1 queries.
* Unbounded queries.
* Expensive repeated queries.
* Missing indexes.
* Unsafe dynamic sorting.
* Unsafe dynamic filtering.
* Excessive relationship loading.
* Excessive pagination.
* Database connection exhaustion.

---

# 30. N+1 Query Role

Avoid:

```php
$orders = Order::all();

foreach ($orders as $order) {
    $order->customer->name;
}
```

Use:

```php
$orders = Order::with('customer')->get();
```

Required practices:

* Eager loading.
* Query monitoring.
* Relationship optimization.
* Selective fields.
* Proper indexing.

---

# 31. Pagination Security Role

Never allow unlimited `per_page`.

Use:

```text
Default: 25
Maximum: 100
```

Example:

```php
$perPage = min(
    (int) $request->input('per_page', 25),
    100
);
```

Values should be tuned according to actual requirements.

---

# 32. Dynamic Query Security

Never directly trust user-controlled:

```text
sort
filter
column
direction
search field
```

Use allowlists.

Example:

```php
$allowedSorts = [
    'name',
    'status',
    'created_at',
];
```

Only allowed values may reach the query.

---

# 33. SQL Injection Protection

Always prefer:

```text
Eloquent
Query Builder
Parameterized Queries
```

Avoid constructing SQL strings using user input.

Never concatenate untrusted values into SQL.

---

# 34. Database Indexing Role

Indexes must be designed around real query patterns.

Review:

```text
Foreign Keys
UUID
Email
Status
Tenant ID
Created At
Reference Number
Frequently searched fields
Frequently filtered fields
```

Use composite indexes when query patterns require them.

Example:

```text
tenant_id + status
tenant_id + created_at
customer_id + status
```

---

# 35. Multi-Tenant Security Role

For multi-tenant systems:

```text
Tenant A
    ↓
Only Tenant A Data

Tenant B
    ↓
Only Tenant B Data
```

Tenant isolation must be enforced at the backend.

Possible mechanisms:

* Global Scopes.
* Tenant Context.
* Middleware.
* Repository filters.
* Policies.
* Database architecture.

Never rely only on frontend filtering.

---

# 36. API Security Role

The API must implement:

* Authentication.
* Authorization.
* Rate limiting.
* Validation.
* Secure serialization.
* Resource transformation.
* Error handling.
* Pagination.
* Request size limits.
* Abuse protection.

---

# 37. API Resource Role

Never expose raw Models directly.

Use:

```text
UserResource
OrderResource
ProductResource
PaymentResource
```

Resources explicitly define the public API representation.

Sensitive database fields must never be exposed.

---

# 38. API Error Handling Role

Use consistent responses.

Success:

```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {}
}
```

Validation:

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {}
}
```

Errors must not expose:

* Stack traces.
* Database credentials.
* SQL statements.
* Internal secrets.
* Sensitive infrastructure information.

---

# 39. DDoS Protection Role

DDoS protection must be layered.

Application-level Laravel protection is NOT sufficient against large volumetric attacks.

Recommended:

```text
Internet
   ↓
CDN / DDoS Protection
   ↓
WAF
   ↓
Load Balancer
   ↓
Laravel
   ↓
Redis
   ↓
Database
```

Infrastructure must absorb malicious traffic before it reaches Laravel whenever possible.

---

# 40. WAF Role

The Web Application Firewall should help protect against:

* Malicious requests.
* Known attack signatures.
* Suspicious traffic.
* Bot abuse.
* Request anomalies.
* Application-layer attacks.

Laravel remains responsible for application-specific authorization and business security.

---

# 41. Rate Limiting Role

Rate limits must be applied to sensitive and expensive operations.

Examples:

```text
Login
Register
Password Reset
OTP
Search
Export
File Upload
Payment
Webhook
Admin Operations
API Requests
```

Limits should be based on the endpoint's risk and cost.

---

# 42. Authentication Abuse Protection

Protect authentication against:

* Brute force.
* Credential stuffing.
* Automated login attempts.
* OTP abuse.
* Password reset abuse.
* Account enumeration.

Use:

```text
Rate Limiting
Temporary Throttling
Audit Logs
MFA where required
Generic authentication errors
```

---

# 43. Expensive Endpoint Protection

Expensive operations must have stronger protection.

Examples:

```text
Large Reports
Exports
Advanced Search
Analytics
Bulk Operations
Data Imports
```

Use:

```text
Rate Limiting
Caching
Pagination
Query Limits
Queues
Authorization
```

---

# 44. Export Architecture

Large exports must run asynchronously.

```text
Export Request
      ↓
Authorization
      ↓
Create Export Job
      ↓
Queue
      ↓
Chunk Processing
      ↓
Generate File
      ↓
Store File
      ↓
Notify User
```

Never allow unlimited synchronous exports.

---

# 45. Idempotency Role

Idempotency is required for critical operations where duplicate requests can cause damage.

Examples:

```text
Payments
Orders
Invoices
Subscriptions
Webhooks
Financial Transactions
```

Flow:

```text
Idempotency Key
       ↓
Already Processed?
    ├── YES → Existing Result
    └── NO  → Process
```

---

# 46. Webhook Security Role

Webhooks must use:

* Signature verification.
* Timestamp verification.
* Replay protection.
* Idempotency.
* Rate limiting.
* Audit logging.

Never trust webhook data without verification.

---

# 47. File Upload Security Role

Uploads must have:

* File size limits.
* MIME validation.
* Extension validation.
* Authorization.
* Safe filenames.
* Secure storage.
* Malware scanning where required.
* Execution prevention.

Uploaded files must never become an uncontrolled execution vector.

---

# 48. Redis Role

Redis may be used for:

```text
Cache
Queue
Rate Limiting
Sessions
Locks
Temporary State
```

Redis must never be publicly exposed.

Use proper network isolation and authentication.

---

# 49. Cache Role

Caching should be used for expensive and frequently accessed data.

Examples:

```text
Configuration
Permissions
Reference Data
Dashboard Statistics
Expensive Queries
```

Every cache must have:

* Clear TTL.
* Invalidation strategy.
* Tenant awareness.
* Permission awareness where applicable.

---

# 50. Cache Stampede Protection

For expensive cache rebuilds:

```text
100 Requests
     ↓
Cache Miss
     ↓
Lock
     ↓
One Request Rebuilds
     ↓
Others Use Result
```

Use distributed locking where required.

---

# 51. Database Availability Role

Protect the database against traffic spikes.

Use:

```text
Caching
Query Optimization
Indexes
Rate Limiting
Connection Management
Queues
Read Optimization
Load Balancing where appropriate
```

The database must not become the first resource exhausted during traffic spikes.

---

# 52. Load Balancing Role

Production environments should support horizontal scaling when required.

```text
                 Load Balancer
                /      |      \
               /       |       \
          Server 1  Server 2  Server N
```

Application servers should remain stateless where possible.

Shared infrastructure:

```text
Redis
Database
Queue
Object Storage
Session Storage
Cache
```

---

# 53. Monitoring Role

Production monitoring must track:

```text
Request Rate
Response Time
5xx Errors
4xx Errors
Database Performance
Slow Queries
CPU
RAM
Queue Length
Failed Jobs
Redis
Cache Hit Ratio
Rate Limit Violations
Authentication Failures
Suspicious Traffic
```

---

# 54. Logging Role

Important events must be logged.

Examples:

```text
Authentication
Authorization Failure
User Creation
Role Changes
Permission Changes
Order Approval
Payment
Security Events
Failed Jobs
External API Failures
```

Never log:

```text
Passwords
API Secrets
Access Tokens
Private Credentials
Sensitive Payment Information
```

---

# 55. Audit Role

Critical business actions must be auditable.

Audit information should include where appropriate:

```text
Actor
Action
Entity
Entity ID
Timestamp
Relevant Metadata
IP / Request Context where justified
```

Audit records must not contain sensitive credentials.

---

# 56. Exception Handling Role

Use dedicated exceptions for meaningful business failures.

Examples:

```text
OrderNotFoundException
InsufficientStockException
PaymentFailedException
UnauthorizedActionException
InvalidStateTransitionException
```

Exceptions should be handled consistently.

---

# 57. State Transition Role

Business entities with states must enforce valid transitions.

Example:

```text
Pending
   ↓
Confirmed
   ↓
Processing
   ↓
Completed
```

Invalid:

```text
Completed
   ↓
Pending
```

State transition rules must be implemented in the business layer.

---

# 58. Concurrency Role

The application must consider race conditions for critical resources.

Examples:

```text
Inventory
Payments
Wallets
Counters
Seats
Bookings
Subscriptions
```

Use appropriate mechanisms:

```text
Database Transactions
Row Locks
Distributed Locks
Unique Constraints
Idempotency
```

---

# 59. Mass Assignment Protection

Models must explicitly control assignable fields.

Use:

```text
$fillable
```

or carefully controlled:

```text
$guarded
```

Never blindly accept arbitrary request data into models.

---

# 60. Authorization Before Business Processing

Sensitive operations must follow:

```text
Authentication
      ↓
Authorization
      ↓
Validation
      ↓
Business Logic
```

Do not perform expensive business processing before verifying that the user is authorized.

---

# 61. Security Headers & Transport

Production deployment should enforce:

* HTTPS.
* Secure cookies.
* HttpOnly cookies where applicable.
* Appropriate SameSite configuration.
* Security headers.
* HSTS where appropriate.
* Secure CORS configuration.

Never use:

```text
Allow-Origin: *
```

for sensitive authenticated APIs unless there is a justified architecture.

---

# 62. Secrets Management

Secrets must never be committed to source control.

Examples:

```text
Database Passwords
API Keys
Payment Credentials
JWT Secrets
OAuth Secrets
Encryption Keys
Redis Credentials
```

Use environment/configuration secret management.

---

# 63. Database Backup Role

Production databases must have:

* Automated backups.
* Retention policy.
* Backup monitoring.
* Restore testing.
* Disaster recovery procedures.

A backup is not considered reliable until restoration has been tested.

---

# 64. Disaster Recovery Role

Define:

```text
RPO
RTO
Backup Strategy
Recovery Strategy
Failover Strategy
Database Recovery
File Recovery
Queue Recovery
```

Critical services must have documented recovery procedures.

---

# 65. Testing Role

Testing must cover:

```text
Unit Tests
Feature Tests
Integration Tests
Authorization Tests
Service Tests
Repository Tests
API Tests
Critical Filament Actions
Security Tests
Performance Tests
```

Business-critical Services receive high test coverage.

---

# 66. Service Testing

Services should be independently testable.

Test:

```text
Valid Input
Invalid Input
Authorization
Transactions
Business Rules
Edge Cases
Concurrency
External Failures
```

---

# 67. Security Testing

The application must be tested against:

```text
SQL Injection
XSS
CSRF
IDOR
Broken Access Control
Mass Assignment
Brute Force
Rate Limit Bypass
File Upload Abuse
Webhook Abuse
Tenant Isolation
Sensitive Data Exposure
```

---

# 68. Performance Testing

Test:

```text
Normal Traffic
Peak Traffic
Concurrent Users
Large Queries
Large Tables
Exports
Imports
Queue Processing
Cache Performance
Database Load
```

Identify bottlenecks before production.

---

# 69. Code Quality Role

Code must follow:

* PSR standards.
* Laravel conventions.
* Clear naming.
* Small classes.
* Small methods.
* Explicit dependencies.
* Meaningful exceptions.
* No duplicated business logic.
* No unnecessary abstractions.

---

# 70. Architecture Review Role

Before merging significant features, verify:

```text
Controller
    ↓
Thin?

Service
    ↓
Correct business logic?

Repository
    ↓
Correct data access?

Policy
    ↓
Authorization enforced?

Filament
    ↓
UI only?

Query
    ↓
Optimized?

Security
    ↓
Protected?

Tests
    ↓
Covered?
```

---

# 71. Forbidden Architecture

The following patterns are prohibited unless there is a documented architectural reason:

```text
Fat Controllers
Fat Models
Fat Filament Resources
Fat Traits
God Services
God Repositories
Direct DB queries everywhere
Business logic in Blade
Business logic in Filament UI callbacks
Business logic in Controllers
External APIs directly from Controllers
Manual Singleton implementations
Duplicated business logic
Uncontrolled dynamic queries
Unbounded database queries
Unprotected exports
Unlimited API requests
Frontend-only authorization
Public Redis
Hardcoded secrets
Raw model API responses
```

---

# 72. Final Responsibility Matrix

| Layer          | Primary Responsibility          |
| -------------- | ------------------------------- |
| Filament       | Admin UI Panel                  |
| Web Controller | Blade rendering & Ajax HTTP     |
| Form Request   | Validation                      |
| Policy         | Authorization                   |
| Service/Action | Business logic & domain workflow|
| Model          | Data representation & Eloquent  |
| Enum           | Fixed domain states             |
| Trait          | Small reusable behavior         |
| Provider       | Dependency registration         |
| Event          | Business occurrence             |
| Listener       | Event reaction                  |
| Job            | Async processing                |
| Notification   | User communication              |
| Observer       | Model lifecycle                 |
| Exception      | Business/application failure    |
| Database       | Persistent data (MySQL)         |

---

# 73. Complete Request Flow

The preferred request flow is:

```text
Client
   ↓
CDN / DDoS Protection
   ↓
WAF
   ↓
Load Balancer
   ↓
Laravel Middleware
   ↓
Authentication
   ↓
Rate Limiting
   ↓
Authorization
   ↓
Controller / Filament Action
   ↓
Form Request / Validation
   ↓
Service
   ↓
Transaction
   ↓
Repository
   ↓
Optimized Eloquent Query
   ↓
Database
   ↓
Event
   ↓
Queue / Notification
   ↓
Resource / Response
```

---

# 74. Final Architecture Principle

The application must follow this rule:

> **Presentation layers should request operations. Services should execute business logic. Repositories should access data. Policies should authorize. Providers should configure dependencies. Infrastructure should protect availability.**

The architecture must ensure that:

```text
API
Filament
Console
Jobs
Scheduled Tasks
```

can all reuse the same business logic without duplication.

---

# 75. Final Production Standards

Before considering the system production-ready, verify:

* [ ] Clean architecture implemented.
* [ ] SOLID principles followed.
* [ ] Controllers are thin.
* [ ] Services contain business workflows.
* [ ] Repositories contain data access.
* [ ] Repository interfaces are bound through Providers.
* [ ] Singleton is used only where justified.
* [ ] Traits contain only reusable small behavior.
* [ ] Policies protect sensitive operations.
* [ ] Filament is used for administration.
* [ ] Filament does not contain duplicated business logic.
* [ ] API and Filament reuse Services.
* [ ] DTOs are used where appropriate.
* [ ] Enums replace magic state strings.
* [ ] Events and Listeners decouple secondary actions.
* [ ] Jobs handle heavy operations.
* [ ] Transactions protect multi-step operations.
* [ ] Idempotency protects critical operations.
* [ ] N+1 queries are prevented.
* [ ] Queries are indexed and optimized.
* [ ] Pagination limits are enforced.
* [ ] Dynamic queries are whitelisted.
* [ ] SQL injection protection is enforced.
* [ ] Tenant isolation is enforced where applicable.
* [ ] API resources protect sensitive fields.
* [ ] Authentication is protected against abuse.
* [ ] Rate limiting is implemented.
* [ ] File uploads are secured.
* [ ] Webhooks are verified.
* [ ] Redis is secured.
* [ ] Caching is implemented where beneficial.
* [ ] Queue failures are monitored.
* [ ] CDN/WAF/DDoS protection exists at infrastructure level.
* [ ] Load balancing is available where required.
* [ ] Database backups are automated.
* [ ] Disaster recovery is defined.
* [ ] Security logging is implemented.
* [ ] Audit logging is implemented.
* [ ] Unit tests exist for critical business logic.
* [ ] Feature/API tests exist.
* [ ] Authorization tests exist.
* [ ] Security tests exist.
* [ ] Performance testing has been performed.
* [ ] Production monitoring is configured.
* [ ] Secrets are managed securely.
* [ ] No critical business logic is duplicated.

---

# 76. Non-Negotiable Rule

> **Do not implement features as simple CRUD operations. Every feature must be evaluated from the perspective of business logic, authorization, database performance, security, concurrency, scalability, observability, and future extensibility.**
