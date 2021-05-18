MAIN_DIR := $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
VIRTUALENV_DIR := $(MAIN_DIR)/venv
VIRTUAL_ENV_DISABLE_PROMPT = true
PATH := $(VIRTUALENV_DIR)/bin:vendor/bin:$(PATH)
SHELL := /usr/bin/env bash

THEME_DIR := $(MAIN_DIR)/madrabbit
NPM_DIR := $(THEME_DIR)/node_modules
VENDOR_DIR := $(THEME_DIR)/vendor
SCSS_DIR := $(THEME_DIR)/scss
CSS_DIR := $(THEME_DIR)/css
JS_DIR := $(THEME_DIR)/js

.DEFAULT_GOAL := help
.SHELLFLAGS := -eu -o pipefail -c

export PATH

help: ## Print this help
	@grep -E '^[a-zA-Z1-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort \
		| awk 'BEGIN { FS = ":.*?## " }; { printf "\033[36m%-30s\033[0m %s\n", $$1, $$2 }'

$(VIRTUALENV_DIR):
	virtualenv -p $(shell command -v python3) $(VIRTUALENV_DIR)

$(VIRTUALENV_DIR)/bin/pre-commit: $(MAIN_DIR)/requirements.txt
	pip install -r $(MAIN_DIR)/requirements.txt
	nodeenv --python-virtualenv --node=15.14.0
	@touch '$(@)'

pre-commit-install: ## Install pre-commit hooks
	pre-commit install

install-pip-packages: $(VIRTUALENV_DIR) $(VIRTUALENV_DIR)/bin/pre-commit ## Install python pip packages in a virtual environment

$(VENDOR_DIR):
	mkdir $(VENDOR_DIR)

$(NPM_DIR)/.bin/sass: $(THEME_DIR)/package.json
	cd $(THEME_DIR); \
	npm install; \
	cp -af ./node_modules/{bootstrap,font-awesome,video.js}/ ./vendor
	@touch '$(@)'

install-npm-packages: $(VENDOR_DIR) $(NPM_DIR)/.bin/sass ## Install npm packages

install: install-pip-packages install-npm-packages

mkcert: ## Create certs if needed
	$(info --> Create certs if needed)
	@if [[ -e mad-rabbit.local-key.pem ]] && [[ -e mad-rabbit.local.pem ]]; then \
		openssl verify -CAfile ~/.local/share/mkcert/rootCA.pem mad-rabbit.local.pem; \
	else \
		mkcert "mad-rabbit.local"; \
	fi; \

tests:
	pre-commit run --all-files

scss: $(CSS_DIR)/theme.css $(CSS_DIR)/theme.prefixed.css $(CSS_DIR)/theme.min.css ## Compile all scss files

clean: ## Remove all generated files
	rm -f $(CSS_DIR)/*.css
	rm -f $(JS_DIR)/*.js
	rm -rf $(VENDOR_DIR)

$(CSS_DIR)/theme.css:
	npx sass --style=expanded --embed-source-map $(SCSS_DIR)/theme.scss $(CSS_DIR)/theme.css

$(CSS_DIR)/theme.prefixed.css:
	npx postcss $(CSS_DIR)/theme.css --use autoprefixer --output  $(CSS_DIR)/theme.prefixed.css

$(CSS_DIR)/theme.min.css:
	npx cleancss $(CSS_DIR)/theme.prefixed.css --output $(CSS_DIR)/theme.min.css --source-map-inline-sources

watch: ## Watch for files changes and compile them if necessary
	npx sass --watch --style=expanded --embed-source-map $(SCSS_DIR)/theme.scss $(CSS_DIR)/theme.css &
	npx postcss --verbose --watch $(CSS_DIR)/theme.css --use autoprefixer --output  $(CSS_DIR)/theme.min.css


.PHONY: help pre-commit-install install-pip-packages install-npm-packages install mkcert tests scss watch
