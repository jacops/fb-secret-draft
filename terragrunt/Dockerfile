FROM devopsinfra/docker-terragrunt:aws-tf-1.2.3-tg-0.38.1

RUN useradd -ms /bin/bash terraform

USER terraform

RUN set -ex && \
    mkdir -p ~/.ssh && \
    chmod 0700 ~/.ssh && \
    ssh-keyscan -t rsa gitlab.com >> ~/.ssh/known_hosts

WORKDIR /workspace
