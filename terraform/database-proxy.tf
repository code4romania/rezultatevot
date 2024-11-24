resource "aws_db_proxy" "main" {
  name                   = local.namespace
  debug_logging          = false
  engine_family          = "MYSQL"
  idle_client_timeout    = 1800
  require_tls            = false
  role_arn               = aws_iam_role.db_proxy_secrets.arn
  vpc_security_group_ids = [aws_security_group.database.id]
  vpc_subnet_ids         = aws_subnet.private.*.id

  auth {
    auth_scheme = "SECRETS"
    iam_auth    = "DISABLED"
    secret_arn  = aws_secretsmanager_secret.rds.arn
  }
}

resource "aws_db_proxy_default_target_group" "main" {
  db_proxy_name = aws_db_proxy.main.name

  connection_pool_config {
    connection_borrow_timeout    = 120
    max_connections_percent      = 75
    max_idle_connections_percent = 50
  }
}

resource "aws_db_proxy_target" "main" {
  db_instance_identifier = aws_db_instance.main.identifier
  db_proxy_name          = aws_db_proxy.main.name
  target_group_name      = aws_db_proxy_default_target_group.main.name
}

resource "aws_iam_role" "db_proxy_secrets" {
  name               = "RDSProxy-${local.namespace}-secrets"
  assume_role_policy = data.aws_iam_policy_document.proxy_secrets_policy.json
}

resource "aws_iam_role_policy" "db_proxy_secrets" {
  name   = "${local.namespace}-secrets"
  role   = aws_iam_role.db_proxy_secrets.id
  policy = data.aws_iam_policy_document.db_proxy_secrets_permissions.json
}

data "aws_iam_policy_document" "proxy_secrets_policy" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["rds.amazonaws.com"]
    }
  }
}

data "aws_iam_policy_document" "db_proxy_secrets_permissions" {
  statement {
    effect    = "Allow"
    actions   = ["secretsmanager:GetSecretValue"]
    resources = [aws_secretsmanager_secret.rds.arn]
  }
}
