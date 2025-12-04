import https from 'https';
import http from 'http';
import { URL } from 'url';

export default async function handler(req, res) {
    const { url } = req.query;

    if (!url) {
        return res.status(400).json({ error: 'URL parameter is required' });
    }

    try {
        const targetUrl = new URL(url);
        const protocol = targetUrl.protocol === 'https:' ? https : http;

        const proxyReq = protocol.get(url, (proxyRes) => {
            // Forward status code
            res.status(proxyRes.statusCode);

            // Forward relevant headers
            const headersToForward = [
                'content-type',
                'content-length',
                'accept-ranges',
                'content-range',
                'cache-control',
                'last-modified',
                'etag'
            ];

            headersToForward.forEach(header => {
                if (proxyRes.headers[header]) {
                    res.setHeader(header, proxyRes.headers[header]);
                }
            });

            // Set CORS headers
            res.setHeader('Access-Control-Allow-Origin', '*');
            res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');

            // Pipe the response
            proxyRes.pipe(res);
        });

        proxyReq.on('error', (err) => {
            console.error('Proxy error:', err);
            res.status(500).json({ error: 'Failed to fetch audio file' });
        });

    } catch (error) {
        console.error('Invalid URL:', error);
        res.status(400).json({ error: 'Invalid URL provided' });
    }
}
