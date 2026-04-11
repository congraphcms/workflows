# congraph/workflows

`congraph/workflows` manages workflow definitions and workflow points used to model state machines for content.

## Responsibilities

- workflow CRUD
- workflow-point CRUD
- validation of transitions and workflow metadata
- repository bindings for workflow state definitions

This package is used by `eav` to handle entity statuses and localized workflow states.

## Installation

```bash
composer require congraph/workflows
```

Laravel package provider:

- [WorkflowsServiceProvider.php](/Users/nikolap/git/np/congraph/packages/workflows/WorkflowsServiceProvider.php)

## Typical usage

Use workflows to define:

- editorial states like draft, review, published
- localized workflow paths
- state endpoints used by entities

## Development

### Install dependencies

```bash
cd /Users/nikolap/git/np/congraph/packages/workflows
composer update -W
```

### Run tests

```bash
composer test
composer test:pgsql
```

The package has both unit coverage and database-backed integration coverage.

## Contribution notes

- prefer PostgreSQL-safe SQL and seeders
- workflow behavior changes should include validator and repository tests
- when changing status logic, also retest `eav` and `entity-elastic`
