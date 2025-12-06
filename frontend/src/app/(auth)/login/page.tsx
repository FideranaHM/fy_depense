//src/app/auth/login/page.tsx
import Link from "next/link";

export default function Login() {
    return (
        <main className="relative isolate flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 dark:bg-slate-900 px-4">
            <button className='absolute top-3 left-4 z-[999] dark:text-gray-300 text-gray-800 '>
                <Link href='/'>
                Accueil
                </Link>
            </button>
            <div
                className="w-50 max-w-xl  rounded-xl flex flex-col md:flex-row items-center gap-8 transition-all duration-1000 
                'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'
                "
            >
                <div className="w-full flex-1 rounded-5xl bg-white/60 dark:bg-gray-800/60 shadow-2xl backdrop-blur-xl md:p-10">
                    <h1 className="text-center text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white md:text-4xl">
                        <>
                            Bienvenue sur{' '}
                            <span className="bg-gradient-to-r from-pink-500 to-blue-400 bg-clip-text text-transparent">
                                FyDepense
                            </span>
                        </>
                    </h1>
                    <form className="mt-8 space-y-5">
                        {/* email */}
                        <div>
                            <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                E-mail <span className="ml-1 text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                name="email"
                                placeholder="vous@exemple.com "
                                className="mt-1 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:placeholder-slate-400"
                             />
                        </div>
                        {/* password*/}
                        <div>
                            <label className="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                Mot de passe <span className="ml-1 text-red-400">*</span>
                            </label>
                            <input
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                className="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 pr-10 text-slate-800 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                             />
                        </div>
                    </form>  
                </div>   
            </div>       
        </main>
    );
}