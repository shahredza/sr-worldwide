-- =====================================================================
-- SR Worldwide demo data. FAKE DATA ONLY, safe to commit.
-- Import after schema.sql.
--
-- Demo admin login:  admin / admin123
-- Change or delete this account before putting the site anywhere public.
-- =====================================================================

SET NAMES utf8mb4;

INSERT INTO admins (username, password_hash) VALUES
  ('admin', '$2y$10$mV9aqYKkDFNiSNOI1gciZ.E25xwg1as9y2FqmVTBJTEEkq.c72S7q'); -- admin123 (demo)

INSERT INTO categories (id, slug, name, sort_order) VALUES
  (1, 'tops',        'Tops',        1),
  (2, 'bottoms',     'Bottoms',     2),
  (3, 'accessories', 'Accessories', 3);

INSERT INTO products (category_id, name, slug, description, price, sizes, image, image_back, is_featured) VALUES
-- Tops
(1, 'Polo Rugby', 'polo-rugby',
 'Stay stylish and sporty with our Polo Rugby shirt, crafted from premium cotton fabric for maximum comfort and breathability. Featuring classic stripes and a modern fit, this piece is perfect for everyday wear or semi-formal occasions. Dress it up or down: your go-to choice for effortless fashion.',
 79.90, 'S,M,L,XL', 'products/baju1.jpg', 'products/modelbaju.jpg', 1),
(1, 'Sweatshirt', 'sweatshirt',
 'Stay cozy and stylish with our Sweatshirt, made from soft cotton blend fabric. Ideal for layering or wearing on its own, this piece combines warmth and comfort for a perfect casual look. Whether you''re lounging or out with friends, this sweatshirt is a must-have addition to your wardrobe.',
 119.90, 'S,M,L,XL', 'products/baju2.jpg', 'products/modelbaju2.jpg', 0),
(1, 'Hoodie', 'hoodie',
 'Our premium Hoodie offers the perfect balance of warmth and style. Made from a soft cotton blend, this hoodie is designed to keep you cozy on colder days. Whether you''re lounging or heading out, it''s a versatile piece that complements any casual look.',
 149.90, 'S,M,L,XL', 'products/hoodie.jpg', 'products/hoodieblakang.jpg', 1),
(1, 'Striped Rugby Sweater', 'striped-rugby-sweater',
 'A heavyweight knit with bold navy and cream stripes and a clean collar. Relaxed through the body so it layers easily over a tee, with ribbed cuffs and hem that hold their shape wash after wash.',
 129.90, 'S,M,L,XL', 'products/modelbajutepi2.jpg', NULL, 0),

-- Bottoms
(2, 'Hear Me Jeans', 'hear-me-jeans',
 'Crafted from premium quality denim, the Hear Me Jeans feature a flattering high-waisted cut perfect for everyday wear. Designed for both comfort and style, they include a clean hidden zip, precise stitching, and a classic straight-leg fit. Whether you''re dressing up or going casual, these jeans offer timeless versatility.',
 139.90, '28,30,32,34,36', 'products/seluar1.jpg', 'products/seluar1blakang.jpg', 1),
(2, 'Corduroy Carpenter Pants', 'corduroy-carpenter-pants',
 'The Corduroy Carpenter Pants offer a blend of rugged durability and contemporary street style. Featuring soft yet strong corduroy fabric, reinforced stitching and multiple functional pockets, these pants are perfect for both casual wear and active lifestyles. The relaxed fit ensures comfort, while the subtle textured design adds a fashion-forward touch.',
 199.90, '28,30,32,34,36', 'products/seluar2.jpg', 'products/seluar2blakang.jpg', 0),
(2, 'Army Pants', 'army-pants',
 'These Army Pants are designed for ultimate comfort and durability, featuring a classic military-inspired design. Crafted from durable fabric, they offer both function and style. The relaxed fit ensures all-day comfort, while the multi-pocket design gives you plenty of space for your essentials.',
 149.90, '28,30,32,34,36', 'products/seluar3.jpg', 'products/seluar3blakang.jpg', 1),
(2, 'Wide Leg Trousers', 'wide-leg-trousers',
 'Fluid black trousers with a high rise and a full, floor-skimming wide leg. A soft drape that moves when you walk, pressed front creases and a clean flat waistband, made to pair with a boxy top.',
 159.90, '28,30,32,34,36', 'products/modebajutepi.jpg', NULL, 0),

-- Accessories
(3, 'Bandana', 'bandana',
 'This stylish bandana is perfect for any occasion. Made from high-quality fabric, it''s comfortable to wear and can be styled in multiple ways. Whether you''re going for a casual look or a more edgy style, this bandana is the perfect accessory for you.',
 50.00, 'One Size', 'products/bandana.jpg', 'products/bandanablakang.jpg', 0),
(3, 'Cap', 'cap',
 'This stylish cap is made with high-quality materials to keep you cool and fashionable. The perfect accessory for any casual outing, offering great comfort and a sleek design. Pair it with a variety of outfits, whether you''re heading out for a walk or meeting up with friends.',
 45.00, 'One Size', 'products/topi.jpg', NULL, 1),
(3, 'Necklace', 'necklace',
 'This elegant necklace is designed to add a touch of sophistication to any outfit. Crafted with high-quality materials and a timeless design, it''s perfect for both casual and formal events.',
 74.90, 'One Size', 'products/rantai.jpg', NULL, 0),
(3, 'Chain & Ring Set', 'chain-ring-set',
 'A full silver-tone set: two chunky chains, a curb bracelet and a handful of rings, delivered in a black leather wallet. Mix and stack them, or split the set with a friend.',
 189.90, 'One Size', 'products/item.jpg', NULL, 0);
