import { Injectable } from '@nestjs/common';

@Injectable()
export class AppService {
  getHello(): string {
    return JSON.parse('{"Name":"Jean","Senha":"ola mundo"}');
  }
}
