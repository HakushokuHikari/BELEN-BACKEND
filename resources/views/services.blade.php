<x-layout>
  <x-slot name="title">Services</x-slot>
  <x-nav :href="route('home')">Homepage</x-nav>
  <x-nav :href="route('contact')">Contact</x-nav>
  <x-nav :href="route('services')">Services</x-nav>
  <x-nav :href="route('about')">About</x-nav>
  Hello from Services!
</x-layout>