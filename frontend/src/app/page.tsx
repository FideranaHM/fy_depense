// src/app/page.tsx
import Link from 'next/link';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPiggyBank } from '@fortawesome/free-solid-svg-icons';

export default function HomePage() {
  return (
    <main className="min-h-screen bg-gradient-to-br from-pink-50 to-blue-100 grid grid-rows-[1fr_auto] px-6 text-center">
      {/* Contenu centré */}
      <div className="flex flex-col items-center justify-center">
        <div className="w-80 h-100 mb-5 text-emerald-600">
          {/* <FontAwesomeIcon icon={faPiggyBank} className="w-full h-full" /> */}
        </div>
        <h1 className="text-4xl md:text-5xl font-extrabold bg-gradient-to-r from-pink-700 to-blue-700 bg-clip-text text-transparent mb-4">
          Gérez vos dépenses quotidiennes en un clin d&rsquo;œil
        </h1>
        <p className="text-lg text-gray-600 mb-8 max-w-2xl">
          Créez des listes d&rsquo;achats, suivez vos produits, analysez vos dépenses.
          Simple, rapide, gratuit.
        </p>

        <div className="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            href="/login"
            className="px-6 py-3 rounded-lg bg-pink-600 text-white font-semibold hover:bg-blue-700 transition"
          >
            Se connecter
          </Link>
          <Link
            href="/register"
            className="px-6 py-3 rounded-lg border-2 border-pink-600 text-pink-600 font-semibold hover:bg-pink-200 transition"
          >
            Créer un compte
          </Link>
        </div>
      </div>

      {/* Footer collé en bas */}
      <footer className="text-sm text-gray-500 py-4">
        © {new Date().getFullYear()} fy_depense – Propulsé par Next.js & Tailwind
      </footer>
    </main>
  );
}