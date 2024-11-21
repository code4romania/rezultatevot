terraform {
  required_version = "~> 1.9"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.76"
    }
  }

  cloud {
    organization = "commitglobal"

    workspaces {
      tags = [
        "rezultatevot"
      ]
    }
  }
}

provider "aws" {
  region = var.region

  default_tags {
    tags = {
      app = "rezultatevot"
      env = var.env
    }
  }
}


provider "aws" {
  alias  = "acm"
  region = "us-east-1"

  default_tags {
    tags = {
      app = "rezultatevot"
      env = var.env
    }
  }
}
