"""
Face verification script.
Usage:
    python verify_face.py <standalone_id_path> <selfie_path>

Output (JSON):
    {"success": true, "match": true, "distance": 0.46, "confidence": 54.0}
    {"success": false, "error": "..."}
"""
import face_recognition
import sys
import json


# ================================================================
# HELPER: Get face encoding from a single image
# ================================================================
def get_face_encoding(image_path):
    try:
        img = face_recognition.load_image_file(image_path)
    except Exception as e:
        return None, f"Cannot load image: {e}"

    # FACE DETECTION — HOG
    locations = face_recognition.face_locations(img, model="hog")

    if len(locations) == 0:
        return None, "No face detected."

    # SORT FACES BY AREA (Just in case there are multiple faces, we take the biggest one)
    def area(loc):
        top, right, bottom, left = loc
        return (bottom - top) * (right - left)

    locations.sort(key=area, reverse=True)

    # FACE ENCODING — CNN (128-D vectors)
    encodings = face_recognition.face_encodings(img, known_face_locations=[locations[0]])

    if not encodings:
        return None, "Could not encode face."

    return encodings[0], None


# ================================================================
# MAIN FUNCTION
# ================================================================
def main():
    # ARGUMENT VALIDATION (Need 2 paths now)
    if len(sys.argv) < 3:
        print(json.dumps({"success": False, "error": "Missing arguments: Need Standalone ID and Selfie paths"}))
        return

    id_path = sys.argv[1]
    selfie_path = sys.argv[2]
    tolerance = 0.6

    # 1. Get ID Face Encoding
    id_encoding, error = get_face_encoding(id_path)
    if error:
        print(json.dumps({"success": False, "error": f"ID Error: {error}"}))
        return

    # 2. Get Selfie Face Encoding
    selfie_encoding, error = get_face_encoding(selfie_path)
    if error:
        print(json.dumps({"success": False, "error": f"Selfie Error: {error}"}))
        return

    # 3. FACE COMPARISON — Euclidean Distance
    distance = face_recognition.face_distance([id_encoding], selfie_encoding)[0]
    is_match = bool(distance < tolerance)

    # OUTPUT RESULT (JSON)
    print(json.dumps({
        "success": True,
        "match": is_match,
        "distance": round(float(distance), 4),
        "confidence": round(float((1 - distance) * 100), 2)
    }))


# ================================================================
# ENTRY POINT
# ================================================================
if __name__ == "__main__":
    main()
