module "ecs_app" {
  source = "./modules/ecs-service"

  depends_on = [
    module.ecs_cluster
  ]

  name         = "${local.namespace}-app"
  cluster_name = module.ecs_cluster.cluster_name
  min_capacity = 1
  max_capacity = 3

  deployment_minimum_healthy_percent = 50
  deployment_maximum_percent         = 200

  image_repo = local.image.repo
  image_tag  = local.image.tag

  use_load_balancer       = true
  lb_dns_name             = aws_lb.main.dns_name
  lb_zone_id              = aws_lb.main.zone_id
  lb_vpc_id               = aws_vpc.main.id
  lb_listener_arn         = aws_lb_listener.http.arn
  lb_hosts                = ["www.${var.domain_name}"]
  lb_health_check_enabled = true
  lb_path                 = "/up"

  container_memory_soft_limit = 512
  container_memory_hard_limit = 1024

  log_group_name                 = module.ecs_cluster.log_group_name
  service_discovery_namespace_id = module.ecs_cluster.service_discovery_namespace_id

  container_port          = 80
  network_mode            = "awsvpc"
  network_security_groups = [aws_security_group.ecs.id]
  network_subnets         = aws_subnet.private.*.id # network_subnets = [aws_subnet.private.0.id]

  task_role_arn          = aws_iam_role.ecs_task_role.arn
  enable_execute_command = var.enable_execute_command

  predefined_metric_type = "ECSServiceAverageCPUUtilization"
  target_value           = 70

  ordered_placement_strategy = [
    {
      type  = "spread"
      field = "instanceId"
    },
    {
      type  = "binpack"
      field = "memory"
    }
  ]

  environment = [
    {
      name  = "APP_NAME"
      value = "Rezultate Vot"
    },
    {
      name  = "APP_ENV"
      value = var.env
    },
    {
      name  = "APP_DEBUG"
      value = tostring(false)
    },
    {
      name  = "APP_URL"
      value = "https://www.${var.domain_name}"
    },
    {
      name  = "AWS_DEFAULT_REGION"
      value = var.region
    },
    {
      name  = "FILESYSTEM_DISK"
      value = "s3"
    },
    {
      name  = "FILAMENT_FILESYSTEM_DISK"
      value = "s3"
    },
    {
      name  = "AWS_BUCKET"
      value = module.s3_public.bucket
    },
    {
      name  = "AWS_URL"
      value = "https://www.${var.domain_name}"
    },
    {
      name  = "SENTRY_TRACES_SAMPLE_RATE"
      value = 0.3
    },
    {
      name  = "SENTRY_PROFILES_SAMPLE_RATE"
      value = 0.5
    },
    {
      name  = "GOOGLE_ANALYTICS_TRACKING_ID"
      value = var.google_analytics_tracking_id
    },
    {
      name  = "AWS_BUCKET_ROOT"
      value = "media"
    },
    {
      name  = "REDIS_HOST"
      value = aws_elasticache_cluster.main.cache_nodes.0.address
    },
    {
      name  = "REDIS_PORT"
      value = aws_elasticache_cluster.main.cache_nodes.0.port
    },
    {
      name  = "SCOUT_DRIVER",
      value = "typesense"
    },
    {
      name  = "PHP_PM_MAX_CHILDREN",
      value = 128
    },
    {
      name  = "DB_HOST",
      value = aws_db_proxy.main.endpoint
    }
  ]

  secrets = [
    {
      name      = "APP_KEY"
      valueFrom = aws_secretsmanager_secret.app_key.arn
    },
    {
      name      = "DB_CONNECTION"
      valueFrom = "${aws_secretsmanager_secret.rds.arn}:engine::"
    },
    # {
    #   name      = "DB_HOST"
    #   valueFrom = "${aws_secretsmanager_secret.rds.arn}:host::"
    # },
    {
      name      = "DB_PORT"
      valueFrom = "${aws_secretsmanager_secret.rds.arn}:port::"
    },
    {
      name      = "DB_DATABASE"
      valueFrom = "${aws_secretsmanager_secret.rds.arn}:database::"
    },
    {
      name      = "DB_USERNAME"
      valueFrom = "${aws_secretsmanager_secret.rds.arn}:username::"
    },
    {
      name      = "DB_PASSWORD"
      valueFrom = "${aws_secretsmanager_secret.rds.arn}:password::"
    },
    {
      name      = "TYPESENSE_HOST"
      valueFrom = "${aws_secretsmanager_secret.typesense.arn}:host::"
    },
    {
      name      = "TYPESENSE_PORT"
      valueFrom = "${aws_secretsmanager_secret.typesense.arn}:port::"
    },
    {
      name      = "TYPESENSE_API_KEY"
      valueFrom = "${aws_secretsmanager_secret.typesense.arn}:key::"
    },
    {
      name      = "SENTRY_DSN"
      valueFrom = aws_secretsmanager_secret.sentry_dsn.arn
    },

  ]

  allowed_secrets = [
    aws_secretsmanager_secret.app_key.arn,
    aws_secretsmanager_secret.typesense.arn,
    aws_secretsmanager_secret.sentry_dsn.arn,
    aws_secretsmanager_secret.rds.arn,
  ]
}

module "s3_public" {
  source = "./modules/s3"

  block_public_acls       = false
  block_public_policy     = false
  ignore_public_acls      = false
  restrict_public_buckets = false

  enable_versioning = var.env == "production"

  name   = "${local.namespace}-public"
  policy = data.aws_iam_policy_document.s3_cloudfront_public.json
}

resource "aws_s3_bucket_cors_configuration" "s3_public" {
  bucket = module.s3_public.bucket

  cors_rule {
    allowed_headers = ["*"]
    allowed_methods = ["GET", "PUT", "POST"]
    allowed_origins = ["https://www.${var.domain_name}"]
    expose_headers  = ["ETag"]
    max_age_seconds = 86400
  }
}

resource "aws_secretsmanager_secret" "app_key" {
  name = "${local.namespace}-secret_key-${random_string.secrets_suffix.result}"
}

resource "aws_secretsmanager_secret_version" "app_key" {
  secret_id     = aws_secretsmanager_secret.app_key.id
  secret_string = random_password.app_key.result
}

resource "aws_secretsmanager_secret" "typesense" {
  name = "${local.namespace}-typesense-${random_string.secrets_suffix.result}"
}

resource "aws_secretsmanager_secret_version" "typesense" {
  secret_id = aws_secretsmanager_secret.typesense.id

  secret_string = jsonencode({
    "host" = "10.0.6.178",
    "port" = "8108",
    "key"  = "xyz"
  })
}

resource "aws_secretsmanager_secret" "sentry_dsn" {
  name = "${local.namespace}-sentry_dsn-${random_string.secrets_suffix.result}"
}

resource "aws_secretsmanager_secret_version" "sentry_dsn" {
  secret_id     = aws_secretsmanager_secret.sentry_dsn.id
  secret_string = var.sentry_dsn
}

resource "random_password" "app_key" {
  length  = 32
  special = true

  lifecycle {
    ignore_changes = [
      length,
      special
    ]
  }
}
