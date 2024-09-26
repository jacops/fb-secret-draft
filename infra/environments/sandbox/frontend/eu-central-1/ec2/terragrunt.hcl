include "root" {
  path   = find_in_parent_folders("root.hcl")
  expose = true
}

terraform {
  source = "${get_repo_root()}//terragrunt/modules/ec2"
}

inputs = {
  name     = "frontend"

  availability_zones = ["eu-central-1a", "eu-central-1b", "eu-central-1b"]

  assign_eip_address = false

  associate_public_ip_address = true

  instance_type = "t3.micro"

  user_data_base64 = filebase64("${get_repo_root()}/terragrunt/configs/frontend/ec2-userdata.sh")

  security_group_rules = [
    {
      type        = "egress"
      from_port   = 0
      to_port     = 65535
      protocol    = "-1"
      cidr_blocks = ["0.0.0.0/0"]
    },
    {
      type        = "ingress"
      from_port   = 22
      to_port     = 22
      protocol    = "tcp"
      cidr_blocks = ["0.0.0.0/0"]
    },
    {
      type        = "ingress"
      from_port   = 80
      to_port     = 80
      protocol    = "tcp"
      cidr_blocks = ["0.0.0.0/0"]
    },
    {
      type        = "ingress"
      from_port   = 443
      to_port     = 443
      protocol    = "tcp"
      cidr_blocks = ["0.0.0.0/0"]
    },
    {
      type        = "ingress"
      from_port   = 53
      to_port     = 53
      protocol    = "udp"
      cidr_blocks = ["0.0.0.0/0"]
    },
  ]

  ssh_public_key_path = "./secrets"
}
