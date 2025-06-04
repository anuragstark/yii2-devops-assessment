# Yii2 DevOps 

A comprehensive DevOps implementation showcasing automated deployment of a Yii2 PHP application using Docker Swarm, NGINX reverse proxy, GitHub Actions CI/CD, and Ansible automation on AWS EC2.

## 📋 Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Prerequisites](#prerequisites)
- [Setup Instructions](#setup-instructions)
- [Deployment Process](#deployment-process)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

## 🏗️ Overview

This project demonstrates a production-ready deployment pipeline that includes:

- **Application**: Yii2 PHP framework with a demo interface
- **Containerization**: Docker with multi-stage builds
- **Orchestration**: Docker Swarm for high availability
- **Reverse Proxy**: NGINX on host (not containerized)
- **CI/CD**: GitHub Actions with automated testing and deployment
- **Infrastructure**: Ansible for automated server configuration
- **Platform**: AWS EC2 with Ubuntu 20.04/22.04

## 🏛️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   GitHub Repo   │───▶│  GitHub Actions │───▶│   Docker Hub    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        AWS EC2 Instance                        │
│  ┌─────────────────┐    ┌─────────────────┐                   │
│  │  NGINX (Host)   │───▶│  Docker Swarm   │                   │
│  │   Port 80       │    │                 │                   │
│  └─────────────────┘    │  ┌─────────────┐│                   │
│                         │  │ Yii2 App #1 ││                   │
│                         │  │  Port 8080  ││                   │
│                         │  └─────────────┘│                   │
│                         │  ┌─────────────┐│                   │
│                         │  │ Yii2 App #2 ││                   │
│                         │  │  Port 8080  ││                   │
│                         │  └─────────────┘│                   │
│                         └─────────────────┘                   │
└─────────────────────────────────────────────────────────────────┘
```

## ✅ Prerequisites

### Local Development
```bash
# Required software
- Git
- Ansible (pip install ansible)
- SSH access to EC2 instance
```

### AWS EC2 Instance
```bash
# Instance requirements
- Ubuntu 20.04/22.04 LTS
- Minimum: t3.medium (2 vCPU, 4GB RAM)
- Security groups: SSH (22), HTTP (80), HTTPS (443), Custom (8080)
- SSH key pair for access
```

### GitHub Setup
```bash
# Required secrets in GitHub repository
DOCKER_HUB_USERNAME      # Your Docker Hub username
DOCKER_HUB_TOKEN         # Docker Hub access token
EC2_HOST                 # EC2 public IP address
EC2_SSH_PRIVATE_KEY      # EC2 SSH private key content
```

### Docker Hub
```bash
# Create repository
https://hub.docker.com/repository/create
# Repository name: yii2-app
```

## 🛠️ Setup Instructions

### Step 1: Clone and Configure Repository

```bash
# Clone the repository
git clone https://github.com/your-username/yii2-devops-assessment.git
cd yii2-devops-assessment

# Update configuration files with your details
# Edit ansible/inventory/hosts.yml - replace YOUR_EC2_PUBLIC_IP
# Edit docker-compose.yml - replace image name if needed
# Edit .github/workflows/deploy.yml - verify Docker Hub username
```

### Step 2: Configure GitHub Secrets

Navigate to `Settings → Secrets and variables → Actions` in your GitHub repository:

```bash
# Add the following secrets:
DOCKER_HUB_USERNAME=your-dockerhub-username
DOCKER_HUB_TOKEN=your-dockerhub-access-token
EC2_HOST=your-ec2-public-ip
EC2_SSH_PRIVATE_KEY=your-ec2-private-key-content
```

### Step 3: Initial Server Setup with Ansible

```bash
# Update inventory file
nano ansible/inventory/hosts.yml
# Replace YOUR_EC2_PUBLIC_IP with actual IP

# Test connectivity
ansible -i ansible/inventory/hosts.yml ec2_servers -m ping

# Run infrastructure setup
ansible-playbook -i ansible/inventory/hosts.yml ansible/playbooks/setup-infrastructure.yml

# Verify setup
ssh ubuntu@YOUR_EC2_IP "docker --version && docker swarm ls"
```

### Step 4: Manual First Deployment (Optional)

```bash
# SSH to EC2 instance
ssh ubuntu@YOUR_EC2_IP

# Clone repository
git clone https://github.com/your-username/yii2-devops-assessment.git
cd yii2-devops-assessment

# Build and deploy
docker build -t your-dockerhub-username/yii2-app:latest -f docker/Dockerfile .
docker stack deploy -c docker-compose.yml yii2-app

# Verify deployment
docker service ls
curl localhost:8080
```

## 🚀 Deployment Process

### Automated Deployment (CI/CD)

The deployment process is triggered automatically on push to the `main` branch:

1. **Build Phase**:
   - Checkout code
   - Build Docker image
   - Push to Docker Hub
   - Security scan with Trivy

2. **Deploy Phase**:
   - SSH to EC2 instance
   - Run Ansible deployment playbook
   - Update Docker Swarm service
   - Health check verification

3. **Rollback Phase** (on failure):
   - Automatic rollback to previous version
   - Service health monitoring
   - Notification of deployment status

### Manual Deployment

```bash
# Trigger deployment manually
git add .
git commit -m "Deploy application"
git push origin main

# Or use GitHub Actions workflow dispatch
# Go to Actions tab → Deploy Yii2 Application → Run workflow
```

## 🧪 Testing

### Local Testing

```bash
# Test Ansible playbooks
ansible-playbook --syntax-check ansible/playbooks/setup-infrastructure.yml
ansible-playbook --check -i ansible/inventory/hosts.yml ansible/playbooks/setup-infrastructure.yml

# Test Docker build
docker build -t yii2-app:test -f docker/Dockerfile .
docker run -p 8080:80 yii2-app:test
```

### Production Testing

```bash
# Health check endpoints
curl -I http://YOUR_EC2_IP/health        # NGINX health
curl -I http://YOUR_EC2_IP/              # Application health

# Service status
ssh ubuntu@YOUR_EC2_IP "docker service ls"
ssh ubuntu@YOUR_EC2_IP "docker service ps yii2-app_yii2-app"

# Load testing (optional)
ab -n 1000 -c 10 http://YOUR_EC2_IP/
```

### Monitoring Commands

```bash
# Check service logs
docker service logs yii2-app_yii2-app

# Monitor service status
watch 'docker service ls'

# Check NGINX status
sudo systemctl status nginx
sudo nginx -t

# Check system resources
htop
df -h
free -h
```

## 🔧 Troubleshooting

### Common Issues

#### 1. Docker Swarm Service Not Starting
```bash
# Check service status
docker service ps yii2-app_yii2-app

# Check logs
docker service logs yii2-app_yii2-app

# Restart service
docker service update --force yii2-app_yii2-app
```

#### 2. NGINX Configuration Issues
```bash
# Test configuration
sudo nginx -t

# Check error logs
sudo tail -f /var/log/nginx/error.log

# Restart NGINX
sudo systemctl restart nginx
```

#### 3. GitHub Actions Failed
```bash
# Check workflow logs in GitHub Actions tab
# Common fixes:
# - Verify secrets are set correctly
# - Check EC2 security groups
# - Ensure SSH key has correct permissions
# - Verify Docker Hub credentials
```

#### 4. Ansible Connection Issues
```bash
# Test connectivity
ansible -i ansible/inventory/hosts.yml ec2_servers -m ping

# Debug SSH connection
ssh -vvv ubuntu@YOUR_EC2_IP

# Check inventory file format
ansible-inventory -i ansible/inventory/hosts.yml --list
```

### Performance Optimization

```bash
# Increase Docker Swarm replicas
docker service scale yii2-app_yii2-app=3

# Monitor resource usage
docker stats

# Optimize NGINX configuration
# Edit /etc/nginx/sites-available/yii2-app
# Add worker_processes, worker_connections tuning
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make changes with tests
4. Submit a pull request


---

**Project Status**: Ready for deployment and testing

**📧 Support**: Create an issue in this repository for support questions.
