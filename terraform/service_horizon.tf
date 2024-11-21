module "ecs_horizon" {
  source = "./modules/ecs-service"

  depends_on = [
    module.ecs_cluster
  ]

  name         = "${local.namespace}-horizon"
  cluster_name = module.ecs_cluster.cluster_name
  min_capacity = 1
  max_capacity = 1

  image_repo = local.image.repo
  image_tag  = local.image.tag

  command = ["php", "/var/www/artisan", "horizon", "--no-interaction"]

  use_load_balancer = false

  container_memory_soft_limit = 3072
  container_memory_hard_limit = 4096

  log_group_name                 = module.ecs_cluster.log_group_name
  service_discovery_namespace_id = module.ecs_cluster.service_discovery_namespace_id

  container_port          = 80
  network_mode            = "awsvpc"
  network_security_groups = [aws_security_group.ecs.id]
  network_subnets         = aws_subnet.private.*.id # network_subnets = [aws_subnet.private.0.id]

  task_role_arn          = aws_iam_role.ecs_task_role.arn
  enable_execute_command = var.enable_execute_command

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
    {
      name      = "DB_HOST"
      valueFrom = "${aws_secretsmanager_secret.rds.arn}:host::"
    },
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
      name      = "SENTRY_DSN"
      valueFrom = aws_secretsmanager_secret.sentry_dsn.arn
    },

  ]

  allowed_secrets = [
    aws_secretsmanager_secret.app_key.arn,
    aws_secretsmanager_secret.sentry_dsn.arn,
    aws_secretsmanager_secret.rds.arn,
  ]
}

