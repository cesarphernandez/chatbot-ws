# Definir variables
PHP_SERVER = php -S localhost:8000 -t public
NGROK = ngrok http 8000

# Comando por defecto
.DEFAULT_GOAL := help

# Ayuda
help:
	@echo "Comandos disponibles:"
	@echo "  make run      - Ejecutar el servidor PHP local"
	@echo "  make ngrok    - Conectar con ngrok"
	@echo "  make stop     - Detener todos los procesos (servidor PHP y ngrok)"

# Ejecutar el servidor PHP local
run:
	@echo "Iniciando servidor PHP en http://localhost:8000"
	@$(PHP_SERVER)

# Conectar con ngrok
ngrok:
	@echo "Conectando con ngrok..."
	@$(NGROK)

# Detener todos los procesos
stop:
	@echo "Deteniendo todos los procesos..."
	@killall php 2>/dev/null || true
	@killall ngrok 2>/dev/null || true
	@echo "Procesos detenidos."

.PHONY: help run ngrok stop