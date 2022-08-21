locals {
  ################ BASE ################
  root_dir                   = get_parent_terragrunt_dir()
  configs_dir                = "${local.root_dir}/configs"
  relative_deployment_path   = path_relative_to_include()
  deployment_path_components = compact(split("/", local.relative_deployment_path))

  ################ DEPLOYMENT CONFIG ################
  possible_config_paths = [
    for i in range(0, length(local.deployment_path_components) + 1) :
    join("/", concat(
      [local.root_dir],
      slice(local.deployment_path_components, 0, i),
      ["config.hcl"]
    ))
  ]
  deployment_config_list = [
    for path in local.possible_config_paths :
    read_terragrunt_config(path).locals if fileexists(path)
  ]
  deployment_config = merge(local.deployment_config_list...)

  ################ CONFIGS ################
  context = read_terragrunt_config("${local.configs_dir}/context.hcl").locals
  common  = read_terragrunt_config("${local.configs_dir}/common.hcl").locals

  region          = can(local.deployment_config.region) ? local.deployment_config.region : local.common.default_region
  account_id      = can(local.deployment_config.account_id) ? local.deployment_config.account_id : local.common.default_account_id
  assume_role_arn = can(local.deployment_config.assume_role_arn) ? local.deployment_config.assume_role_arn : ""

  terraform_state_bucket_name   = can(local.deployment_config.terraform_state_bucket_name) ? local.deployment_config.terraform_state_bucket_name : local.common.terraform_state_bucket_name
  terraform_state_bucket_region = can(local.deployment_config.terraform_state_bucket_region) ? local.deployment_config.terraform_state_bucket_region : local.common.terraform_state_bucket_region
  terraform_lock_dynamodb_table = can(local.deployment_config.terraform_lock_dynamodb_table) ? local.deployment_config.terraform_lock_dynamodb_table : local.common.terraform_lock_dynamodb_table
}

generate "provider_aws" {
  path      = "provider-aws.tf"
  if_exists = "overwrite_terragrunt"
  contents  = templatefile("configs/provider.tftpl", {
    region              = local.region,
    role_arn            = local.assume_role_arn
    allowed_account_ids = local.account_id != "" && local.account_id != null ? "[\"${local.account_id}\"]" : "[]"
  })
}

remote_state {
  backend = "s3"
  config = {
    bucket         = local.terraform_state_bucket_name
    key            = "${path_relative_to_include()}/terraform.tfstate"
    region         = local.terraform_state_bucket_region
    encrypt        = true
    dynamodb_table = local.terraform_lock_dynamodb_table

    # Use configuration from `stacks/base`
    skip_bucket_ssencryption = true
    skip_bucket_root_access  = true
    skip_bucket_enforced_tls = true
  }
  generate = {
    path      = "backend.tf"
    if_exists = "overwrite_terragrunt"
  }
}

inputs = merge(
  local.context,
  local.common,
  local.deployment_config
)
