module "ecs_typesense" {
  source = "./modules/ecs-service"

  depends_on = [
    module.ecs_cluster
  ]

  name         = "${local.namespace}-typesense"
  cluster_name = module.ecs_cluster.cluster_name
  min_capacity = 1
  max_capacity = 1

  deployment_minimum_healthy_percent = 0
  deployment_maximum_percent         = 100

  image_repo = "typesense/typesense"
  image_tag  = "27.1"

  use_load_balancer = false

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
      name  = "TYPESENSE_API_KEY"
      value = "xyz"
    },
    {
      name  = "TYPESENSE_ENABLE_CORS"
      value = tostring(true)
    },
  ]
}

