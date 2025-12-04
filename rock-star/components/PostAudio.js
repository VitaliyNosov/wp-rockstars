import React, { useEffect, useRef, useState } from 'react';
import 'plyr/dist/plyr.css';

const PostAudio = ({ audioUrl }) => {
    const audioRef = useRef(null);
    const waveRef = useRef(null);
    const [isOpen, setIsOpen] = useState(false);
    const plyrInstance = useRef(null);
    const waveSurferInstance = useRef(null);

    useEffect(() => {
        let isCancelled = false;

        const initPlayer = async () => {
            if (typeof window !== 'undefined') {
                const Plyr = (await import('plyr')).default;
                const WaveSurfer = (await import('wavesurfer.js')).default;

                if (isCancelled) return;

                if (audioRef.current && waveRef.current) {
                    // Destroy existing instances if any
                    if (plyrInstance.current) plyrInstance.current.destroy();
                    if (waveSurferInstance.current) waveSurferInstance.current.destroy();

                    // Initialize Plyr
                    plyrInstance.current = new Plyr(audioRef.current, {
                        controls: ['play', 'current-time', 'mute', 'volume'],
                    });

                    // Initialize WaveSurfer
                    waveSurferInstance.current = WaveSurfer.create({
                        container: waveRef.current,
                        waveColor: '#2E3038',
                        progressColor: '#4A6CF7',
                        barWidth: 2,
                        barGap: 2,
                        barRadius: 1,
                        height: 80,
                        responsive: true,
                        interact: true,
                        dragToSeek: true,
                        backend: 'MediaElement',
                        media: audioRef.current,
                    });

                    // Sync seek
                    waveSurferInstance.current.on('seek', (progress) => {
                        if (audioRef.current && audioRef.current.duration) {
                            audioRef.current.currentTime = progress * audioRef.current.duration;
                        }
                    });
                }
            }
        };

        if (audioUrl) {
            initPlayer();
        }

        return () => {
            isCancelled = true;
            if (plyrInstance.current) {
                plyrInstance.current.destroy();
                plyrInstance.current = null;
            }
            if (waveSurferInstance.current) {
                waveSurferInstance.current.destroy();
                waveSurferInstance.current = null;
            }
        };
    }, [audioUrl]);

    const toggleOpen = () => {
        if (isOpen) {
            // Close
            if (plyrInstance.current) {
                plyrInstance.current.pause();
            }
            setIsOpen(false);
        } else {
            // Open
            setIsOpen(true);
        }
    };

    if (!audioUrl) return null;

    return (
        <div className="rs-listen-toggle-container" style={{ marginTop: '50px', maxWidth: '100%', width: '100%' }}>
            <button
                onClick={toggleOpen}
                aria-expanded={isOpen}
                className={`inline-flex items-center justify-center py-2 px-4 mr-4 mb-2 rounded-md bg-primary bg-opacity-10 text-body-color hover:bg-opacity-100 hover:text-white cursor-pointer transition ${isOpen ? 'border-none' : 'border border-transparent'}`}
                style={{ textDecoration: 'none' }}
                type="button"
            >
                <span className="inline-block w-5 h-5 mr-2" aria-hidden="true">
                    {isOpen ? (
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                            <path d="M18 6L6 18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M6 6L18 18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                    ) : (
                        <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    )}
                </span>
                <span>Listen to the article</span>
            </button>

            <div
                className={`rs-post-audio-player-container ${isOpen ? 'is-open' : ''}`}
                style={{
                    maxHeight: isOpen ? '300px' : '0',
                    opacity: isOpen ? '1' : '0',
                    overflow: 'hidden',
                    transition: 'opacity .4s ease, max-height .5s ease',
                    border: '1px solid #2E3038',
                    borderRadius: '6px',
                    color: '#fff',
                    marginTop: '10px',
                    padding: '8px 12px 16px',
                    position: 'relative' // Ensure relative positioning for children
                }}
            >
                {/* Waveform */}
                <div ref={waveRef} className="rs-audio-wave" style={{ width: '100%', height: '80px' }}></div>

                {/* Audio element */}
                <audio
                    ref={audioRef}
                    className="js-player"
                    crossOrigin="anonymous"
                    style={{ width: '100%', height: '60px', marginTop: '8px' }}
                >
                    <source src={audioUrl} type="audio/mpeg" />
                </audio>

                <style jsx global>{`
          .rs-post-audio-player-container .plyr__progress,
          .rs-post-audio-player-container .plyr__progress__container {
            display: none !important;
          }
          .rs-post-audio-player-container .plyr {
            width: 100% !important;
            height: 60px !important;
          }
          .rs-post-audio-player-container .plyr__time {
            color: #fff !important;
          }
          .plyr--audio .plyr__controls {
            background: rgba(0, 0, 0, 0) !important;
          }
        `}</style>
            </div>
        </div>
    );
};

export default PostAudio;
