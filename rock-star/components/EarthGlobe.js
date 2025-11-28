// Earth.js lazy loading component
import { useEffect, useRef, useState } from 'react';
import Script from 'next/script';

export default function EarthGlobe({ connections = [] }) {
    const earthRef = useRef(null);
    const [isLoaded, setIsLoaded] = useState(false);

    useEffect(() => {
        if (!isLoaded || typeof window === 'undefined' || !window.Earth) return;

        const home_url = window.location.origin;
        let myearth;
        const sprites = [];

        myearth = new window.Earth('myearth', {
            location: { lat: 20, lng: 20 },
            light: 'none',
            mapImage: home_url + '/wp-content/uploads/2025/07/hologram-map.svg',
            transparent: true,
            autoRotate: true,
            autoRotateSpeed: 1.2,
            autoRotateDelay: 100,
            autoRotateStart: 2000,
        });

        myearth.addEventListener('ready', function () {
            this.startAutoRotate();

            // Add connection lines
            const line = {
                color: '#4A6CF7',
                opacity: 0.35,
                hairline: true,
                offset: -0.9
            };

            for (let i in connections) {
                line.locations = [
                    { lat: connections[i][0], lng: connections[i][1] },
                    { lat: connections[i][2], lng: connections[i][3] }
                ];
                this.addLine(line);
            }

            // Add shine sprites
            for (let i = 0; i < 8; i++) {
                sprites[i] = this.addSprite({
                    image: home_url + '/wp-content/uploads/2025/07/hologram-shine.svg',
                    scale: 0.01,
                    offset: -0.9,
                    opacity: 0.9
                });
                pulse(i);
            }
        });

        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min)) + min;
        }

        function pulse(index) {
            const random_location = connections[getRandomInt(0, connections.length - 1)];
            sprites[index].location = { lat: random_location[0], lng: random_location[1] };

            sprites[index].animate('scale', 0.5, {
                duration: 320,
                complete: function () {
                    this.animate('scale', 0.01, {
                        duration: 320,
                        complete: function () {
                            setTimeout(function () { pulse(index); }, getRandomInt(100, 400));
                        }
                    });
                }
            });
        }

        return () => {
            // Cleanup if needed
            if (myearth) {
                myearth = null;
            }
        };
    }, [isLoaded, connections]);

    return (
        <>
            <Script
                src="/common/js/earth.js"
                strategy="lazyOnload"
                onLoad={() => setIsLoaded(true)}
            />
            <div id="myearth" ref={earthRef} style={{ width: '100%', height: '600px' }}></div>
        </>
    );
}

// Default connections data
export const defaultConnections = [
    [59.651901245117, 17.918600082397, 41.8002778, 12.2388889],
    [59.651901245117, 17.918600082397, 51.4706, -0.461941],
    [13.681099891662598, 100.74700164794922, -6.1255698204, 106.65599823],
    [13.681099891662598, 100.74700164794922, 28.566499710083008, 77.10310363769531],
    // ... add all other connections from custom.js
];
