#!/bin/bash

# Tour Image Download Script - Final Version
# Using direct working image URLs from Pexels (free stock photos)

echo "========================================="
echo "Tour Image Downloader - Final"
echo "Using Pexels free stock photos"
echo "========================================="
echo ""

BASE_DIR="D:/xampp82/htdocs/ssst3/public/images/tours"

download_image() {
    local url="$1"
    local output="$2"
    local description="$3"

    echo "Downloading: $description"
    echo "  From: $url"

    curl -L -o "$output" "$url" --max-time 60 --retry 3 -H "User-Agent: Mozilla/5.0"

    size=$(stat -c%s "$output" 2>/dev/null || stat -f%z "$output" 2>/dev/null)
    if [ $? -eq 0 ] && [ $size -gt 10000 ]; then
        echo "  ✓ Success ($(numfmt --to=iec $size 2>/dev/null || echo $size) bytes)"
    else
        echo "  ✗ Failed (file too small: $size bytes)"
    fi
    echo ""
}

echo "Downloading Kyrgyzstan images..."
echo "========================================="
echo ""

# Kyrgyzstan images from Pexels
download_image \
    "https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/kyrgyzstan-nomad/song-kul-yurts-mountains.webp" \
    "Mountains landscape"

download_image \
    "https://images.pexels.com/photos/163236/luxury-hotel-bedroom-guest-room-163236.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/kyrgyzstan-nomad/nomadic-family-yurt.webp" \
    "Traditional interior"

echo "Downloading Kazakhstan-Kyrgyzstan images..."
echo "========================================="
echo ""

download_image \
    "https://images.pexels.com/photos/1770809/pexels-photo-1770809.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/kz-kg-nature/skazka-canyon.webp" \
    "Rock formations"

download_image \
    "https://images.pexels.com/photos/346529/pexels-photo-346529.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/kz-kg-nature/ala-archa-gorge.webp" \
    "Mountain gorge"

download_image \
    "https://images.pexels.com/photos/1098365/pexels-photo-1098365.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/kz-kg-nature/big-almaty-lake.webp" \
    "Mountain lake"

echo "Downloading Pamir Highway images..."
echo "========================================="
echo ""

download_image \
    "https://images.pexels.com/photos/210243/pexels-photo-210243.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/pamir-silk-road/pamir-highway-mountain-road.webp" \
    "Mountain road"

download_image \
    "https://images.pexels.com/photos/1559825/pexels-photo-1559825.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/pamir-silk-road/ak-baital-pass-4655m.webp" \
    "Mountain pass"

download_image \
    "https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/pamir-silk-road/wakhan-corridor-afghanistan.webp" \
    "Mountain valley"

download_image \
    "https://images.pexels.com/photos/147411/italy-mountains-dawn-daybreak-147411.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/pamir-silk-road/iskanderkul-lake.webp" \
    "Alpine lake"

download_image \
    "https://images.pexels.com/photos/1350789/pexels-photo-1350789.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/pamir-silk-road/pamiri-homestay.webp" \
    "Traditional home"

download_image \
    "https://images.pexels.com/photos/2166559/pexels-photo-2166559.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/pamir-silk-road/registan-samarkand.webp" \
    "Islamic architecture"

download_image \
    "https://images.pexels.com/photos/3881872/pexels-photo-3881872.jpeg?auto=compress&cs=tinysrgb&w=1200" \
    "$BASE_DIR/pamir-silk-road/bukhara-poi-kalyan.webp" \
    "Mosque architecture"

echo "========================================="
echo "Download Complete!"
echo "========================================="
echo ""
echo "Summary of downloaded files:"
cd "$BASE_DIR"
find . -name "*.webp" -o -name "*.jpg" | sort
echo ""
echo "Images with good size (>10KB):"
find . \( -name "*.webp" -o -name "*.jpg" \) -size +10k -exec ls -lh {} \; | awk '{print $5 "\t" $9}' | sort -k2
