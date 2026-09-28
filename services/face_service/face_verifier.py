#!/usr/bin/env python3
"""
Face Verification Service for Employee Attendance
Supports both CLI mode and HTTP Microservice mode.
Uses face_recognition (dlib 128-d face encodings).
"""

import sys
import os
import json
import argparse

try:
    import numpy as np
    import face_recognition
    from PIL import Image, ImageOps
    HAS_FACE_REC = True
except ImportError:
    HAS_FACE_REC = False


def load_and_orient_image(image_path, max_dim=1600):
    """
    Loads an image file, automatically corrects EXIF orientation (crucial for phone cameras),
    ensures RGB format, and resizes if excessively large to ensure fast processing.
    """
    with Image.open(image_path) as pil_img:
        # Automatically transpose based on EXIF orientation (phones/cameras)
        pil_img = ImageOps.exif_transpose(pil_img)
        if pil_img.mode != 'RGB':
            pil_img = pil_img.convert('RGB')
        
        # Resize if dimensions exceed max_dim for performance
        w, h = pil_img.size
        if max(w, h) > max_dim:
            scale = max_dim / float(max(w, h))
            new_size = (int(w * scale), int(h * scale))
            pil_img = pil_img.resize(new_size, Image.Resampling.LANCZOS)
            
        return np.array(pil_img)


def get_face_encodings_robust(img_np):
    """
    Extracts face encodings with fallbacks:
    1. Standard face encodings
    2. Upsampling (x2) for small or distant faces
    3. Rotation checks (90, 180, 270 degrees) in case orientation metadata was stripped
    """
    # 1. Standard detection
    encs = face_recognition.face_encodings(img_np)
    if encs:
        return encs

    # 2. Upsample x2
    locs = face_recognition.face_locations(img_np, number_of_times_to_upsample=2)
    if locs:
        encs = face_recognition.face_encodings(img_np, known_face_locations=locs)
        if encs:
            return encs

    # 3. Try rotations (90, 180, 270 degrees)
    pil_img = Image.fromarray(img_np)
    for angle in [90, 180, 270]:
        rotated_arr = np.array(pil_img.rotate(angle, expand=True))
        encs = face_recognition.face_encodings(rotated_arr)
        if encs:
            return encs
        locs = face_recognition.face_locations(rotated_arr, number_of_times_to_upsample=2)
        if locs:
            encs = face_recognition.face_encodings(rotated_arr, known_face_locations=locs)
            if encs:
                return encs

    return []


def get_face_locations_robust(img_np):
    """
    Detects face bounding box locations with orientation and upsample fallbacks.
    """
    locs = face_recognition.face_locations(img_np)
    if locs:
        return locs

    locs = face_recognition.face_locations(img_np, number_of_times_to_upsample=2)
    if locs:
        return locs

    pil_img = Image.fromarray(img_np)
    for angle in [90, 180, 270]:
        rotated_arr = np.array(pil_img.rotate(angle, expand=True))
        locs = face_recognition.face_locations(rotated_arr)
        if locs:
            return locs
        locs = face_recognition.face_locations(rotated_arr, number_of_times_to_upsample=2)
        if locs:
            return locs

    return []


def compare_faces(ref_path, query_path, tolerance=0.50):
    """
    Compares two face images using 128-dimensional face embeddings.
    tolerance: default 0.50 (strict match, default dlib is 0.60, 0.50 is safer for attendance security)
    """
    if not HAS_FACE_REC:
        return {
            "status": False,
            "match": False,
            "error_code": "MODULE_NOT_LOADED",
            "message": "face_recognition module is not installed or available."
        }

    if not os.path.exists(ref_path):
        return {
            "status": False,
            "match": False,
            "error_code": "REF_FILE_NOT_FOUND",
            "message": f"Reference image not found: {ref_path}"
        }

    if not os.path.exists(query_path):
        return {
            "status": False,
            "match": False,
            "error_code": "QUERY_FILE_NOT_FOUND",
            "message": f"Selfie image not found: {query_path}"
        }

    try:
        # Load reference image with EXIF correction
        ref_image = load_and_orient_image(ref_path)
        ref_encodings = get_face_encodings_robust(ref_image)

        if not ref_encodings:
            return {
                "status": False,
                "match": False,
                "error_code": "NO_FACE_IN_REF",
                "message": "Face not detected in registered profile photo."
            }

        ref_encoding = ref_encodings[0]

        # Load query / selfie image with EXIF correction
        query_image = load_and_orient_image(query_path)
        query_encodings = get_face_encodings_robust(query_image)

        if not query_encodings:
            return {
                "status": False,
                "match": False,
                "error_code": "NO_FACE_IN_QUERY",
                "message": "Face not detected in selfie. Please take a clear photo."
            }

        query_encoding = query_encodings[0]

        # Compute Euclidean distance (smaller = closer match)
        distance = float(face_recognition.face_distance([ref_encoding], query_encoding)[0])

        # Convert distance to similarity percentage (0.0 dist -> 100%, 0.6 dist -> 50%, >=1.0 -> 0%)
        # Linear/Normalized mapping: similarity = max(0, min(100, (1.0 - distance) * 100))
        similarity = max(0.0, min(100.0, round((1.0 - distance) * 100.0, 2)))
        is_match = bool(distance <= tolerance)

        return {
            "status": True,
            "match": is_match,
            "distance": round(distance, 4),
            "similarity": similarity,
            "tolerance": tolerance,
            "face_detected_ref": True,
            "face_detected_query": True,
            "message": "Face verified successfully." if is_match else "Face does not match registered employee."
        }

    except Exception as e:
        return {
            "status": False,
            "match": False,
            "error_code": "EXCEPTION",
            "message": str(e)
        }


def detect_face(image_path):
    """Detects if at least one face exists in the image."""
    if not HAS_FACE_REC:
        return {"status": False, "has_face": False, "message": "Module not available"}
    if not os.path.exists(image_path):
        return {"status": False, "has_face": False, "message": "File not found"}
    try:
        img = load_and_orient_image(image_path)
        locations = get_face_locations_robust(img)
        return {
            "status": True,
            "has_face": len(locations) > 0,
            "face_count": len(locations),
            "locations": locations
        }
    except Exception as e:
        return {"status": False, "has_face": False, "message": str(e)}


def start_flask_service(host="127.0.0.1", port=5005):
    """Starts a lightweight HTTP server for fast in-memory verification without re-importing libraries."""
    from flask import Flask, request, jsonify

    app = Flask(__name__)

    @app.route("/health", methods=["GET"])
    def health():
        return jsonify({"status": "ok", "service": "stafo_face_verifier"})

    @app.route("/verify", methods=["POST"])
    def verify():
        data = request.get_json(silent=True) or request.form
        ref_path = data.get("reference_image") or data.get("ref_image")
        query_path = data.get("query_image") or data.get("captured_image")
        tolerance = float(data.get("tolerance", 0.50))

        if not ref_path or not query_path:
            return jsonify({
                "status": False,
                "match": False,
                "message": "reference_image and query_image paths are required."
            }), 400

        result = compare_faces(ref_path, query_path, tolerance)
        return jsonify(result), 200

    @app.route("/detect", methods=["POST"])
    def detect():
        data = request.get_json(silent=True) or request.form
        image_path = data.get("image_path")
        if not image_path:
            return jsonify({"status": False, "message": "image_path is required"}), 400
        result = detect_face(image_path)
        return jsonify(result), 200

    print(f"Starting Face Verification Service on http://{host}:{port}...")
    app.run(host=host, port=port, threaded=True)


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Face Verification Engine")
    parser.add_argument("--compare", nargs=2, metavar=("REF", "QUERY"), help="Compare two image files")
    parser.add_argument("--detect", metavar="IMAGE", help="Detect faces in an image")
    parser.add_argument("--tolerance", type=float, default=0.50, help="Face distance tolerance (default: 0.50)")
    parser.add_argument("--server", action="store_true", help="Run HTTP server mode")
    parser.add_argument("--host", default="127.0.0.1", help="Server host")
    parser.add_argument("--port", type=int, default=5005, help="Server port")

    args = parser.parse_args()

    if args.server:
        start_flask_service(host=args.host, port=args.port)
    elif args.compare:
        res = compare_faces(args.compare[0], args.compare[1], tolerance=args.tolerance)
        print(json.dumps(res))
    elif args.detect:
        res = detect_face(args.detect)
        print(json.dumps(res))
    else:
        parser.print_help()
