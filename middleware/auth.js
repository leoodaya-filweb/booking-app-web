const jwt = require('jsonwebtoken');
const blacklist = new Set();

module.exports = (req, res, next) => {
    const token = req.header('Authorization').replace('Bearer ', '');
    if (!token) {
        return res.status(401).json({ error: 'Access denied' });
    }
    if (blacklist.has(token)) {
        return res.status(401).json({ error: 'Token has been invalidated' });
    }
    try {
        const decoded = jwt.verify(token, 'your_jwt_secret');
        req.user = decoded;
        next();
    } catch (error) {
        res.status(400).json({ error: 'Invalid token' });
    }
};

// Function to invalidate a token
module.exports.invalidateToken = (token) => {
    blacklist.add(token);
};
