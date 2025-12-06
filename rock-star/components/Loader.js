export default function Loader() {
    return (
        <>
            <style jsx global>{`
                @keyframes spin {
                    from {
                        transform: rotate(0deg);
                    }
                    to {
                        transform: rotate(360deg);
                    }
                }
                .loader-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background-color: rgba(255, 255, 255, 0.8);
                }
                html.dark .loader-overlay {
                    background-color: rgba(0, 0, 0, 0.8);
                }
                .spinner {
                    width: 64px;
                    height: 64px;
                    border: 4px solid #4A6CF7;
                    border-top-color: transparent;
                    border-radius: 50%;
                    animation: spin 0.8s linear infinite;
                }
            `}</style>
            <div className="loader-overlay">
                <div className="spinner"></div>
            </div>
        </>
    );
}
