#!/bin/bash

# Build the Mario model from the Modelfile
echo "Building Mario model..."
ollama create Mario -f Modelfile

# Start the Ollama server
echo "Starting Ollama server..."
ollama serve 