include "root" {
  path   = find_in_parent_folders("root.hcl")
  expose = true
}

terraform {
  source = "tfr:///cloudposse/s3-website/aws//.?version=0.17.3"
}

inputs = {
  name     = "frontend"

  hostname         = "fb-secret-draft.jacops.pl"
  parent_zone_name = "jacops.pl"
}
