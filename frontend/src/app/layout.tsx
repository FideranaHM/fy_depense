// src/app/layout.tsx
import './globals.css';

export const metadata = {
  title: 'fy_depense – Gérez vos dépenses quotidiennes',
  description: 'Créez des listes, suivez vos achats, maîtrisez votre budget.',
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="fr">
      <body>{children}</body>
    </html>
  );
}