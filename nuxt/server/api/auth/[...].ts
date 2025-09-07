import CredentialsProvider from '@auth/core/providers/credentials'
import { NuxtAuthHandler } from '#auth'

export default NuxtAuthHandler({
  secret: useRuntimeConfig().authSecret,
  providers: [
    CredentialsProvider({
      name: 'credentials',
      credentials: {
        email: { label: 'Email', type: 'email' },
        password: { label: 'Password', type: 'password' }
      },
      async authorize(credentials: any) {
        try {
          const config = useRuntimeConfig()
          const response = await $fetch('/login', {
            method: 'POST',
            baseURL: config.public.apiBaseUrl,
            body: {
              email: credentials?.email,
              password: credentials?.password
            }
          })

          if (response && response.user && response.token) {
            return {
              id: response.user.id,
              email: response.user.email,
              name: response.user.name,
              role: response.user.role,
              token: response.token
            }
          }
          return null
        } catch (error) {
          console.error('Auth error:', error)
          return null
        }
      }
    })
  ],
  session: {
    strategy: 'jwt'
  },
  callbacks: {
    async jwt({ token, user }) {
      if (user) {
        token.role = user.role
        token.accessToken = user.token
      }
      return token
    },
    async session({ session, token }) {
      if (token) {
        session.user.role = token.role
        session.accessToken = token.accessToken
      }
      return session
    }
  }
})