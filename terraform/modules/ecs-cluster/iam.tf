### IAM Resources
data "aws_iam_policy_document" "ecs" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["ec2.amazonaws.com"]
    }
  }
}

resource "aws_iam_role" "ecs" {
  name               = "${var.name}-ecs-instance"
  path               = var.iam_path
  assume_role_policy = data.aws_iam_policy_document.ecs.json
  tags               = var.tags
}

resource "aws_iam_role_policy_attachment" "AmazonEC2ContainerServiceforEC2Role" {
  role       = aws_iam_role.ecs.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonEC2ContainerServiceforEC2Role"
}

resource "aws_iam_role_policy_attachment" "AmazonSSMManagedInstanceCore" {
  role       = aws_iam_role.ecs.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_role_policy_attachment" "CloudWatchAgentAdminPolicy" {
  role       = aws_iam_role.ecs.name
  policy_arn = "arn:aws:iam::aws:policy/CloudWatchAgentAdminPolicy"
}

resource "aws_iam_instance_profile" "ecs" {
  name = "${var.name}-ecs-instance"
  role = aws_iam_role.ecs.name
}
