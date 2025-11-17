#!/bin/bash

# Tour Image Download Script
# Downloads royalty-free travel images from Unsplash for tour galleries
# All images are free to use for commercial purposes

echo "========================================="
echo "Tour Image Downloader"
echo "========================================="
echo ""

# Base directory
BASE_DIR="D:/xampp82/htdocs/ssst3/public/images/tours"

# Function to download image with retry
download_image() {
    local url="$1"
    local output="$2"
    local description="$3"

    echo "Downloading: $description"
    echo "  URL: $url"
    echo "  Output: $output"

    curl -L -o "$output" "$url" --max-time 60 --retry 3

    if [ $? -eq 0 ]; then
        echo "  ✓ Success"
    else
        echo "  ✗ Failed"
    fi
    echo ""
}

echo "Creating directories..."
mkdir -p "$BASE_DIR/kyrgyzstan-nomad"
mkdir -p "$BASE_DIR/kz-kg-nature"
mkdir -p "$BASE_DIR/pamir-silk-road"
echo ""

echo "========================================="
echo "TOUR 28: Kyrgyzstan Nomadic Adventure"
echo "========================================="
echo ""

# Kyrgyzstan - Song Kul Lake images
download_image \
    "https://images.unsplash.com/photo-1566127444850-75e3122a9554?w=1200&q=80" \
    "$BASE_DIR/kyrgyzstan-nomad/song-kul-yurts-mountains.webp" \
    "Song Kul yurts with mountains"

download_image \
    "https://images.unsplash.com/photo-1551632811-561732d1e306?w=1200&q=80" \
    "$BASE_DIR/kyrgyzstan-nomad/horseback-riding-song-kul.webp" \
    "Horseback riding in Kyrgyzstan"

download_image \
    "https://images.unsplash.com/photo-1583582095394-d0b8e5d1f3de?w=1200&q=80" \
    "$BASE_DIR/kyrgyzstan-nomad/nomadic-family-yurt.webp" \
    "Traditional yurt interior"

download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/kyrgyzstan-nomad/altyn-arashan-valley.webp" \
    "Mountain valley landscape"

download_image \
    "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1200&q=80" \
    "$BASE_DIR/kyrgyzstan-nomad/issyk-kul-sunset.webp" \
    "Lake sunset"

download_image \
    "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=1200&q=80" \
    "$BASE_DIR/kyrgyzstan-nomad/jeti-oguz-seven-bulls.webp" \
    "Red rock formations"

download_image \
    "https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=1200&q=80" \
    "$BASE_DIR/kyrgyzstan-nomad/traditional-felt-making.webp" \
    "Traditional crafts"

echo "========================================="
echo "TOUR 29: Kazakhstan-Kyrgyzstan Nature"
echo "========================================="
echo ""

# Kazakhstan/Kyrgyzstan nature images
download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/kz-kg-nature/charyn-canyon-valley-castles.webp" \
    "Canyon landscape"

download_image \
    "https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?w=1200&q=80" \
    "$BASE_DIR/kz-kg-nature/altyn-emel-singing-dunes.webp" \
    "Sand dunes"

download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/kz-kg-nature/issyk-kul-mountains.webp" \
    "Lake with mountains"

download_image \
    "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=1200&q=80" \
    "$BASE_DIR/kz-kg-nature/jeti-oguz-red-rocks.webp" \
    "Red rock formations"

download_image \
    "https://images.unsplash.com/photo-1551632811-561732d1e306?w=1200&q=80" \
    "$BASE_DIR/kz-kg-nature/skazka-canyon.webp" \
    "Fairy tale canyon"

download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/kz-kg-nature/ala-archa-gorge.webp" \
    "Alpine gorge"

download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/kz-kg-nature/big-almaty-lake.webp" \
    "Turquoise mountain lake"

echo "========================================="
echo "TOUR 30: Pamir Highway & Silk Road"
echo "========================================="
echo ""

# Pamir Highway and Silk Road images
download_image \
    "https://images.unsplash.com/photo-1464037866556-6812c9d1c72e?w=1200&q=80" \
    "$BASE_DIR/pamir-silk-road/pamir-highway-mountain-road.webp" \
    "Mountain highway"

download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/pamir-silk-road/ak-baital-pass-4655m.webp" \
    "High mountain pass"

download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/pamir-silk-road/wakhan-corridor-afghanistan.webp" \
    "Mountain corridor"

download_image \
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" \
    "$BASE_DIR/pamir-silk-road/iskanderkul-lake.webp" \
    "Turquoise alpine lake"

download_image \
    "https://images.unsplash.com/photo-1583582095394-d0b8e5d1f3de?w=1200&q=80" \
    "$BASE_DIR/pamir-silk-road/pamiri-homestay.webp" \
    "Traditional homestay"

download_image \
    "https://images.unsplash.com/photo-1564507592333-c60657eea523?w=1200&q=80" \
    "$BASE_DIR/pamir-silk-road/registan-samarkand.webp" \
    "Registan Square Samarkand"

download_image \
    "https://images.unsplash.com/photo-1586724237569-f3d0c1dee8c6?w=1200&q=80" \
    "$BASE_DIR/pamir-silk-road/bukhara-poi-kalyan.webp" \
    "Bukhara architecture"

echo "========================================="
echo "Download Complete!"
echo "========================================="
echo ""
echo "Images saved to: $BASE_DIR"
echo ""
echo "Next steps:"
echo "1. Check downloaded images"
echo "2. Replace with better quality photos if needed"
echo "3. Images are already referenced in database"
