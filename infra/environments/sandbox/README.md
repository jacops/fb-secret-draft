# Sandbox environment

## Directory structure
Each environment can have multiple workloads deployed to different data centres.
Please follow the proposed directory structure.
```shell
./environments/sandbox
├── README.md
├── config.hcl
├── networking
│   ├── config.hcl
│   └── eu-central-1
│       │── config.hcl
│       │── vpc
│       │   └── terragrunt.hcl
│       └── direct-connect
│           └── terragrunt.hcl
└── data-processing
    ├── config.hcl
    ├── eu-central-1
    │   │── config.hcl
    │   │── eks
    │   │   └── terragrunt.hcl
    │   └── emr
    │       └── terragrunt.hcl
    └── eu-west-1
        │── config.hcl
        │── eks
        │   └── terragrunt.hcl
        └── emr
            └── terragrunt.hcl
```

## Configuration
Each directory can have a `config.hcl` file with the `locals` section that will be passed to the child stacks.
These configs are merged into one before they are passed to a Terraform module. If there are duplicates in locals names, 
the deepest one takes the precedence.

### Example
`./environment/sandbox/config.hcl`:
```hcl
locals {
  foo  = "bar"
  name = "example"
}
```

`./environment/sandbox/workload/config.hcl`:
```hcl
locals {
  foo = "baz"
}
```

The stack in `./environment/sandbox/workload/aks/` will have the following values being passed:
```hcl
foo  = "bar"
name = "example"
```

## Usage

### Single stack deployment
In order to deploy specific part of the system, please enter one of the child directories
that represents the part of the system (it needs to have `terragrunt.hcl` file) and run typical terragrunt commands in there.

#### Plan
```shell
terragrunt plan -out tfplan
```

#### apply
```shell
terragrunt apply tfplan
```

#### Tear down the stack
```shell
terragrunt destroy
```

### Entire environment deployment
Sometimes you would like to plan and deploy entire environment rather than doing it
on per workload basis. In that case you can take advantage of `run-all` command.
In order to execute the commands from below, remain in this directory as `run-all` command will search recursively 
for `terragrunt.hcl` files in all subdirectories and their dependencies.
> For more information visit the documentation: 
> https://terragrunt.gruntwork.io/docs/features/execute-terraform-commands-on-multiple-modules-at-once/

#### Plan
```shell
terragrunt run-all plan
```

#### apply
```shell
terragrunt run-all apply
```
> Bear in mind that `run-all` command will never use output from a plan command. 

#### Tear down the environment
```shell
terragrunt run-all destroy
```
