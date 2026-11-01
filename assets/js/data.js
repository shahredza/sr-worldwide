// SR Worldwide: demo data (replaces the database in this design-only version).
// Everything here is fake sample data.

window.SR = window.SR || {};

SR.categories = [
  { slug: 'tops', name: 'Tops' },
  { slug: 'bottoms', name: 'Bottoms' },
  { slug: 'accessories', name: 'Accessories' },
];

SR.products = [
  // Tops
  { id: 1, cat: 'tops', name: 'Polo Rugby', slug: 'polo-rugby', price: 79.9, sizes: ['S', 'M', 'L', 'XL'],
    image: 'products/baju1.jpg', back: 'products/modelbaju.jpg', featured: true,
    description: 'Stay stylish and sporty with our Polo Rugby shirt, crafted from premium cotton fabric for maximum comfort and breathability. Featuring classic stripes and a modern fit, this piece is perfect for everyday wear or semi-formal occasions. Dress it up or down: your go-to choice for effortless fashion.' },
  { id: 2, cat: 'tops', name: 'Sweatshirt', slug: 'sweatshirt', price: 119.9, sizes: ['S', 'M', 'L', 'XL'],
    image: 'products/baju2.jpg', back: 'products/modelbaju2.jpg',
    description: 'Stay cozy and stylish with our Sweatshirt, made from soft cotton blend fabric. Ideal for layering or wearing on its own, this piece combines warmth and comfort for a perfect casual look. Whether you’re lounging or out with friends, this sweatshirt is a must-have addition to your wardrobe.' },
  { id: 3, cat: 'tops', name: 'Hoodie', slug: 'hoodie', price: 149.9, sizes: ['S', 'M', 'L', 'XL'],
    image: 'products/hoodie.jpg', back: 'products/hoodieblakang.jpg', featured: true,
    description: 'Our premium Hoodie offers the perfect balance of warmth and style. Made from a soft cotton blend, this hoodie is designed to keep you cozy on colder days. Whether you’re lounging or heading out, it’s a versatile piece that complements any casual look.' },
  { id: 4, cat: 'tops', name: 'Striped Rugby Sweater', slug: 'striped-rugby-sweater', price: 129.9, sizes: ['S', 'M', 'L', 'XL'],
    image: 'products/modelbajutepi2.jpg',
    description: 'A heavyweight knit with bold navy and cream stripes and a clean collar. Relaxed through the body so it layers easily over a tee, with ribbed cuffs and hem that hold their shape wash after wash.' },

  // Bottoms
  { id: 5, cat: 'bottoms', name: 'Hear Me Jeans', slug: 'hear-me-jeans', price: 139.9, sizes: ['28', '30', '32', '34', '36'],
    image: 'products/seluar1.jpg', back: 'products/seluar1blakang.jpg', featured: true,
    description: 'Crafted from premium quality denim, the Hear Me Jeans feature a flattering high-waisted cut perfect for everyday wear. Designed for both comfort and style, they include a clean hidden zip, precise stitching, and a classic straight-leg fit. Whether you’re dressing up or going casual, these jeans offer timeless versatility.' },
  { id: 6, cat: 'bottoms', name: 'Corduroy Carpenter Pants', slug: 'corduroy-carpenter-pants', price: 199.9, sizes: ['28', '30', '32', '34', '36'],
    image: 'products/seluar2.jpg', back: 'products/seluar2blakang.jpg',
    description: 'The Corduroy Carpenter Pants offer a blend of rugged durability and contemporary street style. Featuring soft yet strong corduroy fabric, reinforced stitching and multiple functional pockets, these pants are perfect for both casual wear and active lifestyles. The relaxed fit ensures comfort, while the subtle textured design adds a fashion-forward touch.' },
  { id: 7, cat: 'bottoms', name: 'Army Pants', slug: 'army-pants', price: 149.9, sizes: ['28', '30', '32', '34', '36'],
    image: 'products/seluar3.jpg', back: 'products/seluar3blakang.jpg', featured: true,
    description: 'These Army Pants are designed for ultimate comfort and durability, featuring a classic military-inspired design. Crafted from durable fabric, they offer both function and style. The relaxed fit ensures all-day comfort, while the multi-pocket design gives you plenty of space for your essentials.' },
  { id: 8, cat: 'bottoms', name: 'Wide Leg Trousers', slug: 'wide-leg-trousers', price: 159.9, sizes: ['28', '30', '32', '34', '36'],
    image: 'products/modebajutepi.jpg',
    description: 'Fluid black trousers with a high rise and a full, floor-skimming wide leg. A soft drape that moves when you walk, pressed front creases and a clean flat waistband, made to pair with a boxy top.' },

  // Accessories
  { id: 9, cat: 'accessories', name: 'Bandana', slug: 'bandana', price: 50, sizes: ['One Size'],
    image: 'products/bandana.jpg', back: 'products/bandanablakang.jpg',
    description: 'This stylish bandana is perfect for any occasion. Made from high-quality fabric, it’s comfortable to wear and can be styled in multiple ways. Whether you’re going for a casual look or a more edgy style, this bandana is the perfect accessory for you.' },
  { id: 10, cat: 'accessories', name: 'Cap', slug: 'cap', price: 45, sizes: ['One Size'],
    image: 'products/topi.jpg',
    description: 'This stylish cap is made with high-quality materials to keep you cool and fashionable. The perfect accessory for any casual outing, offering great comfort and a sleek design. Pair it with a variety of outfits, whether you’re heading out for a walk or meeting up with friends.' },
  { id: 11, cat: 'accessories', name: 'Necklace', slug: 'necklace', price: 74.9, sizes: ['One Size'],
    image: 'products/rantai.jpg',
    description: 'This elegant necklace is designed to add a touch of sophistication to any outfit. Crafted with high-quality materials and a timeless design, it’s perfect for both casual and formal events.' },
  { id: 12, cat: 'accessories', name: 'Chain & Ring Set', slug: 'chain-ring-set', price: 189.9, sizes: ['One Size'],
    image: 'products/item.jpg',
    description: 'A full silver-tone set: two chunky chains, a curb bracelet and a handful of rings, delivered in a black leather wallet. Mix and stack them, or split the set with a friend.' },
];

// Sample admin data (fake people, example.com addresses).
SR.sampleUsers = [
  { id: 1, email: 'aina.demo@example.com', birthday: '2001-05-12', joined: '2026-09-02' },
  { id: 2, email: 'hakim.demo@example.com', birthday: '1999-11-23', joined: '2026-09-14' },
  { id: 3, email: 'mei.demo@example.com', birthday: '2003-02-08', joined: '2026-10-03' },
  { id: 4, email: 'ravi.demo@example.com', birthday: '1998-07-30', joined: '2026-10-21' },
];

SR.sampleOrders = [
  { id: 1004, userId: 1, fullname: 'Aina Sofea', nickname: 'Aina', street: '12 Jalan Demo 3/4', city: 'Lumut', state: 'Perak', postcode: '32200',
    status: 'pending', date: '2026-10-30T15:20:00', items: [{ name: 'Hoodie', size: 'M', price: 149.9, qty: 1 }, { name: 'Cap', size: 'One Size', price: 45, qty: 1 }] },
  { id: 1003, userId: 3, fullname: 'Tan Mei Ling', nickname: 'Mei', street: '8 Lorong Contoh', city: 'Georgetown', state: 'Pulau Pinang', postcode: '10200',
    status: 'shipped', date: '2026-10-24T11:05:00', items: [{ name: 'Hear Me Jeans', size: '28', price: 139.9, qty: 1 }] },
  { id: 1002, userId: 2, fullname: 'Hakim Rahman', nickname: 'Kim', street: '21 Jalan Sampel', city: 'Shah Alam', state: 'Selangor', postcode: '40000',
    status: 'completed', date: '2026-10-11T19:42:00', items: [{ name: 'Army Pants', size: '32', price: 149.9, qty: 1 }, { name: 'Polo Rugby', size: 'L', price: 79.9, qty: 2 }] },
  { id: 1001, userId: 4, fullname: 'Ravi Kumar', nickname: 'Ravi', street: '5 Jalan Ujian', city: 'Ipoh', state: 'Perak', postcode: '30000',
    status: 'cancelled', date: '2026-09-28T09:15:00', items: [{ name: 'Necklace', size: 'One Size', price: 74.9, qty: 1 }] },
];

SR.sampleMessages = [
  { id: 2, name: 'Hakim Rahman', email: 'hakim.demo@example.com', subject: 'Restock of Army Pants?', message: 'Hi! Will size 30 of the Army Pants be back soon?', date: '2026-10-29T10:12:00', read: false },
  { id: 1, name: 'Mei', email: 'mei.demo@example.com', subject: 'Collab idea', message: 'Hello SR Worldwide team, I run a small photography page and would love to shoot your next drop.', date: '2026-10-18T16:40:00', read: true },
];

SR.statuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];

SR.states = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis',
  'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'W.P. Kuala Lumpur', 'W.P. Labuan', 'W.P. Putrajaya'];
