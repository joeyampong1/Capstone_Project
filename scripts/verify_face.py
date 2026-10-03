"""
Face verification script.
Usage:
    python verify_face.py <selfie_with_id_path>

Output (JSON):
    {"success": true, "match": true, "distance": 0.46, "confidence": 54.0}
    {"success": false, "error": "..."}
"""
import face_recognition
import sys
import json


# ================================================================
# MAIN FUNCTION
# ================================================================
def main():
    # ------------------------------------------------------------
    # ARGUMENT VALIDATION
    # ------------------------------------------------------------
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "Missing selfie path"}))
        return

    selfie_path = sys.argv[1]
    tolerance = 0.6

    # ------------------------------------------------------------
    # LOAD IMAGE
    # ------------------------------------------------------------
    try:
        img = face_recognition.load_image_file(selfie_path)
    except Exception as e:
        print(json.dumps({"success": False, "error": f"Cannot load image: {e}"}))
        return

    # ------------------------------------------------------------
    # FACE DETECTION — HOG (Histogram of Oriented Gradients)
    # ------------------------------------------------------------
    locations = face_recognition.face_locations(img, model="hog")

    if len(locations) < 2:
        print(json.dumps({
            "success": False,
            "error": f"Need 2 faces in selfie (you + ID), found {len(locations)}"
        }))
        return

    # ------------------------------------------------------------
    # SORT FACES BY AREA
    # ------------------------------------------------------------
    # Biggest = live face (user)
    # Smallest = ID face (printed on card)
    def area(loc):
        top, right, bottom, left = loc
        return (bottom - top) * (right - left)

    locations.sort(key=area, reverse=True)

    # ------------------------------------------------------------
    # FACE ENCODING — CNN (128-D vectors)
    # ------------------------------------------------------------
    encodings = face_recognition.face_encodings(img, known_face_locations=locations)

    if len(encodings) < 2:
        print(json.dumps({"success": False, "error": "Could not encode faces"}))
        return

    live_encoding = encodings[0]     # biggest face (live)
    id_encoding = encodings[-1]      # smallest face (ID)

    # ------------------------------------------------------------
    # FACE COMPARISON — Euclidean Distance
    # ------------------------------------------------------------
    distance = face_recognition.face_distance([live_encoding], id_encoding)[0]
    is_match = bool(distance < tolerance)

    # ------------------------------------------------------------
    # OUTPUT RESULT (JSON)
    # ------------------------------------------------------------
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
