include "root" {
  path   = find_in_parent_folders("root.hcl")
  expose = true
}

terraform {
  source = "tfr:///cloudposse/tfstate-backend/aws//.?version=0.38.1"
}

inputs = {
  # Below variable set only to have "Name" tag simmilar to 
  # the actual resource names
  name = "tf-state"

  s3_bucket_name      = include.root.locals.terraform_state_bucket_name
  dynamodb_table_name = include.root.locals.terraform_lock_dynamodb_table

  # DynamoDB PAY_PER_REQUEST seems like a better (cheaper) solution for now
  billing_mode                  = "PAY_PER_REQUEST"
  enable_point_in_time_recovery = false

  # Configure context
  stage = "base"
  tags = merge(include.root.inputs.tags, {
    TerraformStack = "base"
  })
}

# Hardcoded and overwritten terraform backend configuration
# as this is a "base" stack that creates state s3 bucket 
# (so it should store it's state locally in GIT)
remote_state {
  backend = "local"
  config = {
    path = "${get_terragrunt_dir()}/terraform.tfstate"
  }
  generate = {
    path      = "backend.tf"
    if_exists = "overwrite_terragrunt"
  }
}
