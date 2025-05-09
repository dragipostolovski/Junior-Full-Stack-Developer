import React, { createContext, useContext, useState, useEffect } from 'react';

export type CartItem = {
  productId: string;
  name: string;
  image: string;
  options: Record<string, string>;
  quantity: number;
  price: number;
  availableOptions: Record<string, string[]>;
};

type CartContextType = {
  cart: CartItem[];
  addToCart: (item: Omit<CartItem, 'quantity'>) => void;
  removeFromCart: (item: CartItem) => void;
  updateQuantity: (item: CartItem, delta: number) => void;
  clearCart: () => void;
};

const CartContext = createContext<CartContextType | undefined>(undefined);

export const useCart = () => useContext(CartContext)!;

export const CartProvider: React.FC<{children: React.ReactNode}> = ({ children }) => {
  const [cart, setCart] = useState<CartItem[]>(() => {
    const saved = localStorage.getItem('cart');
    return saved ? JSON.parse(saved) : [];
  });

  useEffect(() => {
    localStorage.setItem('cart', JSON.stringify(cart));
  }, [cart]);

  const addToCart = (item: Omit<CartItem, 'quantity'>) => {
    setCart(prev => {
      const idx = prev.findIndex(
        ci => ci.productId === item.productId &&
              JSON.stringify(ci.options) === JSON.stringify(item.options)
      );
      if (idx > -1) {
        const updated = [...prev];
        updated[idx].quantity += 1;
        return updated;
      }
      return [...prev, { ...item, quantity: 1 }];
    });
  };

  const removeFromCart = (item: CartItem) => {
    setCart(prev => prev.filter(
      ci => !(ci.productId === item.productId && JSON.stringify(ci.options) === JSON.stringify(item.options))
    ));
  };

  const updateQuantity = (item: CartItem, delta: number) => {
    setCart(prev => prev.flatMap(ci => {
      if (ci.productId === item.productId && JSON.stringify(ci.options) === JSON.stringify(item.options)) {
        const newQty = ci.quantity + delta;
        if (newQty <= 0) return [];
        return [{ ...ci, quantity: newQty }];
      }
      return [ci];
    }));
  };

  const clearCart = () => setCart([]);

  return (
    <CartContext.Provider value={{ cart, addToCart, removeFromCart, updateQuantity, clearCart }}>
      {children}
    </CartContext.Provider>
  );
};