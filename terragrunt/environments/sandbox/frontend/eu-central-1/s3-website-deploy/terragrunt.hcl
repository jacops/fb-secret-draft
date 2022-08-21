include "root" {
  path   = find_in_parent_folders("root.hcl")
  expose = true
}

terraform {
  source = "git::git@gitlab.gluzdov.com:devops/terraform_modules.git//terraform-aws-s3-bucket/modules/object"
}

dependency "s3_website" {
  config_path = "../s3-website"
}

inputs = {
  bucket = dependency.s3_website.outputs.s3_bucket_name
  key    =
}
