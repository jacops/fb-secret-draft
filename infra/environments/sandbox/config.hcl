locals {
  environment = "sandbox"

  # Default terraform state configuration
  terraform_state_bucket_name   = "fbsd-sandbox-tf-state"
  terraform_state_bucket_region = "eu-central-1"
  terraform_lock_dynamodb_table = "fbsd-sandbox-tf-state-locks"
}
