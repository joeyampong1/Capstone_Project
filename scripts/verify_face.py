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


def main():
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "Missing selfie path"}))
        return

    selfie_path = sys.argv[1]
    tolerance = 0.6

    try:
        img = face_recognition.load_image_file(selfie_path)
    except Exception as e:
        print(json.dumps({"success": False, "error": f"Cannot load image: {e}"}))
        return

    # Detect faces
    locations = face_recognition.face_locations(img, model="hog")

    if len(locations) < 2:
        print(json.dumps({
            "success": False,
            "error": f"Need 2 faces in selfie (you + ID), found {len(locations)}"
        }))
        return

    # Sort by area — biggest = live face, smallest = ID face
    def area(loc):
        top, right, bottom, left = loc
        return (bottom - top) * (right - left)

    locations.sort(key=area, reverse=True)

    encodings = face_recognition.face_encodings(img, known_face_locations=locations)

    if len(encodings) < 2:
        print(json.dumps({"success": False, "error": "Could not encode faces"}))
        return

    live_encoding = encodings[0]
    id_encoding = encodings[-1]

    distance = face_recognition.face_distance([live_encoding], id_encoding)[0]
    is_match = bool(distance < tolerance)

    print(json.dumps({
        "success": True,
        "match": is_match,
        "distance": round(float(distance), 4),
        "confidence": round(float((1 - distance) * 100), 2)
    }))


if __name__ == "__main__":
    main()