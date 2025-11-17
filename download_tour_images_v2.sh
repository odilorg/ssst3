#!/bin/bash

# Tour Image Download Script V2
# Using direct Unsplash download links and Pexels

echo "========================================="
echo "Tour Image Downloader V2"
echo "========================================="
echo ""

BASE_DIR="D:/xampp82/htdocs/ssst3/public/images/tours"

download_image() {
    local url="$1"
    local output="$2"
    local description="$3"

    echo "Downloading: $description"

    curl -L -o "$output" "$url" --max-time 60 --retry 3 --silent --show-error

    # Check if file is valid (more than 1KB)
    size=$(stat -c%s "$output" 2>/dev/null || stat -f%z "$output" 2>/dev/null)
    if [ $? -eq 0 ] && [ $size -gt 1024 ]; then
        echo "  ✓ Success ($size bytes)"
    else
        echo "  ✗ Failed (file too small or missing)"
    fi
    echo ""
}

echo "========================================="
echo "Re-downloading failed images + Pamir"
echo "========================================="
echo ""

# Kyrgyzstan - better sources
download_image \
    "https://source.unsplash.com/1200x800/?kyrgyzstan,yurt,mountains" \
    "$BASE_DIR/kyrgyzstan-nomad/song-kul-yurts-mountains.webp" \
    "Song Kul yurts with mountains"

download_image \
    "https://source.unsplash.com/1200x800/?yurt,traditional,interior" \
    "$BASE_DIR/kyrgyzstan-nomad/nomadic-family-yurt.webp" \
    "Traditional yurt interior"

# Kazakhstan/Kyrgyzstan - remaining
download_image \
    "https://source.unsplash.com/1200x800/?fairy,tale,canyon,rocks" \
    "$BASE_DIR/kz-kg-nature/skazka-canyon.webp" \
    "Skazka Fairy Tale Canyon"

download_image \
    "https://source.unsplash.com/1200x800/?alpine,gorge,trekking" \
    "$BASE_DIR/kz-kg-nature/ala-archa-gorge.webp" \
    "Ala-Archa gorge"

download_image \
    "https://source.unsplash.com/1200x800/?turquoise,mountain,lake" \
    "$BASE_DIR/kz-kg-nature/big-almaty-lake.webp" \
    "Big Almaty Lake"

echo "========================================="
echo "TOUR 30: Pamir Highway & Silk Road"
echo "========================================="
echo ""

download_image \
    "https://source.unsplash.com/1200x800/?mountain,highway,road" \
    "$BASE_DIR/pamir-silk-road/pamir-highway-mountain-road.webp" \
    "Pamir Highway"

download_image \
    "https://source.unsplash.com/1200x800/?high,mountain,pass,snow" \
    "$BASE_DIR/pamir-silk-road/ak-baital-pass-4655m.webp" \
    "Ak-Baital Pass 4655m"

download_image \
    "https://source.unsplash.com/1200x800/?mountain,corridor,valley" \
    "$BASE_DIR/pamir-silk-road/wakhan-corridor-afghanistan.webp" \
    "Wakhan Corridor"

download_image \
    "https://source.unsplash.com/1200x800/?turquoise,alpine,lake" \
    "$BASE_DIR/pamir-silk-road/iskanderkul-lake.webp" \
    "Iskanderkul Lake"

download_image \
    "https://source.unsplash.com/1200x800/?traditional,homestay,family" \
    "$BASE_DIR/pamir-silk-road/pamiri-homestay.webp" \
    "Pamiri homestay"

download_image \
    "https://source.unsplash.com/1200x800/?samarkand,registan,uzbekistan" \
    "$BASE_DIR/pamir-silk-road/registan-samarkand.webp" \
    "Registan Samarkand"

download_image \
    "https://source.unsplash.com/1200x800/?bukhara,mosque,architecture" \
    "$BASE_DIR/pamir-silk-road/bukhara-poi-kalyan.webp" \
    "Bukhara Poi-Kalyan"

echo "========================================="
echo "Download Complete!"
echo "========================================="
echo ""
echo "Checking file sizes..."
cd "$BASE_DIR"
find . -name "*.webp" -exec ls -lh {} \; | awk '{print $5 "\t" $9}'
