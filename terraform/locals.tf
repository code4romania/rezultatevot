locals {
  namespace = "rezultatevot-v2-${var.env}"
  image = {
    repo = "code4romania/rezultatevot",
    tag  = "0.1.1"
  }
  #   availability_zone = data.aws_availability_zones.current.names[0]

  networking = {
    cidr_block = "10.0.0.0/16"

    public_subnets = [
      "10.0.1.0/24",
      "10.0.2.0/24",
      "10.0.3.0/24"
    ]

    private_subnets = [
      "10.0.4.0/24",
      "10.0.5.0/24",
      "10.0.6.0/24"
    ]
  }
}
