FROM python:3.10-slim

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    PYTHONPATH=/var/www/services/face_service/venv/lib/python3.10/site-packages

WORKDIR /var/www

# Install runtime dependencies for dlib, face_recognition, and Pillow
RUN apt-get update && apt-get install -y --no-install-recommends \
    libopenblas0 \
    liblapack3 \
    curl \
    && rm -rf /var/lib/apt/lists/*

COPY docker/python/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 5005

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["python3", "services/face_service/face_verifier.py", "--server", "--host", "0.0.0.0", "--port", "5005"]
