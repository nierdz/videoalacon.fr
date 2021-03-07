SHELL := /usr/bin/env bash

help: ## Print this help
	@grep -E '^[a-zA-Z1-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort \
		| awk 'BEGIN { FS = ":.*?## " }; { printf "\033[36m%-30s\033[0m %s\n", $$1, $$2 }'

mkcert: ## Create certs if needed
	$(info --> Create certs if needed)
	@if [[ -e mad-rabbit.local-key.pem ]] && [[ -e mad-rabbit.local.pem ]]; then \
		openssl verify -CAfile /home/nierdz/.local/share/mkcert/rootCA.pem mad-rabbit.local.pem; \
	else \
		mkcert "mad-rabbit.local"; \
	fi; \

clean: ## Wipe all data
	$(info --> Wipe all data)
	( \
		docker-compose down; \
		docker volume rm mad-rabbitcom_bedrock-volume; \
		sudo rm -rf mysql; \
	)
