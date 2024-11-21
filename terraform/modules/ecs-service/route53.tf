# A record
resource "aws_route53_record" "ipv4" {
  count = var.lb_domain_zone_id != null ? length(var.lb_hosts) : 0

  zone_id = var.lb_domain_zone_id
  name    = var.lb_hosts[count.index]
  type    = "A"

  alias {
    name                   = var.lb_dns_name
    zone_id                = var.lb_zone_id
    evaluate_target_health = true
  }
}

# AAAA record
resource "aws_route53_record" "ipv6" {
  count = var.lb_domain_zone_id != null ? length(var.lb_hosts) : 0

  zone_id = var.lb_domain_zone_id
  name    = var.lb_hosts[count.index]
  type    = "AAAA"

  alias {
    name                   = var.lb_dns_name
    zone_id                = var.lb_zone_id
    evaluate_target_health = true
  }
}
