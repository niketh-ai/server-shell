export default async function handler(req, res) {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const { user_key, hwid } = req.body;

    if (!user_key || !hwid) {
      return res.status(400).json({
        status: false,
        reason: 'Missing user_key or hwid'
      });
    }

    // Simple validation - always accept test keys
    if (user_key === 'test123' || user_key === 'demo123') {
      const token = generateToken(user_key, hwid);
      
      return res.json({
        status: true,
        data: {
          token: token,
          EXP: '2024-12-31',
          mod_status: 'active',
          rng: Math.floor(Date.now() / 1000)
        }
      });
    }

    // Add your custom license validation here
    // You can connect to a database later
    
    return res.json({
      status: false,
      reason: 'Invalid license key'
    });

  } catch (error) {
    return res.status(500).json({
      status: false,
      reason: 'Server error'
    });
  }
}

function generateToken(user_key, hwid) {
  const crypto = require('crypto');
  
  const payload = {
    user_key: user_key,
    hwid: hwid,
    timestamp: Date.now(),
    expires: Date.now() + 3600000
  };

  const encoded = Buffer.from(JSON.stringify(payload)).toString('base64');
  const signature = crypto
    .createHmac('sha256', 'reaper_nightmare_2024')
    .update(encoded)
    .digest('hex');

  return `${encoded}.${signature}`;
}