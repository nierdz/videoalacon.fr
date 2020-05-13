SHELL := /usr/bin/env bash

mkcert: ## Create certs if needed
	@if [[ -e mad-rabbit.com-key.pem ]] && [[ -e mad-rabbit.com.pem ]]; then \
		openssl verify -CAfile /home/nierdz/.local/share/mkcert/rootCA.pem mad-rabbit.com.pem; \
	else \
		mkcert "mad-rabbit.com"; \
	fi; \
