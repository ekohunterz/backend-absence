 <div class="space-y-3 mt-6">
     <div id="check-in-btn" onclick="openCheckInModal()"
         class="relative overflow-hidden group cursor-pointer rounded-xl border-2 border-primary-500 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
         <div
             class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
         </div>

         <div class="relative p-6 flex items-center justify-center gap-3">
             <div class="flex-shrink-0">
                 <div
                     class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center group-hover:rotate-12 transition-transform duration-300">
                     <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                     </svg>
                 </div>
             </div>
             <div class="flex-1 text-left">
                 <p class="text-xl font-bold text-white uppercase tracking-wide">
                     <span id="btn-text">Memuat lokasi...</span>
                 </p>
                 <p class="text-sm text-white/80 mt-1">
                     Tap untuk melakukan presensi masuk
                 </p>
             </div>
             <div class="flex-shrink-0">
                 <svg class="w-6 h-6 text-white group-hover:translate-x-1 transition-transform duration-300"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                 </svg>
             </div>
         </div>
     </div>

     <div class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
         <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
             <path fill-rule="evenodd"
                 d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                 clip-rule="evenodd" />
         </svg>
         <span>Pastikan Anda berada dalam radius {{ $setting->radius ?? 100 }} meter dari sekolah</span>
     </div>
 </div>
