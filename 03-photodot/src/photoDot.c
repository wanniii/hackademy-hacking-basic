// Name: photoDot.c
// Compile: gcc -o photoDot photoDot.c -fno-stack-protector -no-pie -zexecstack
//
// "image" 파일을 읽어 점(.)으로 된 도트 이미지로 바꾸는 (장난감) 이미지 필터 앱.
// 취약점: dotty() 가 100바이트 버퍼에 strcpy 로 최대 200바이트를 복사 -> 스택 버퍼 오버플로우.
#include <stdio.h>
#include <string.h>

void dotty(char* mem){
    char filter[100];

    strcpy(filter, mem);          // ❌ 길이 검사 없음 -> 오버플로우

    int i;
    for(i = 0; i < 100; i++) {
      if(filter[i]=='\0'){
        break;
      }
      if(filter[i] != '\x20' && filter[i] != '\x0A') {
        filter[i] = '.';
      }
      printf("%c", filter[i]);
    }
    printf("\n\n");
    printf("Dot Filter Applied!!\n");
}

void hidden(){
  printf("###########################################\n");
  printf("############# id : Jaeho Jeon #############\n");
  printf("############# pw : qwer1234 ###############\n");
  printf("###########################################\n");
  fflush(stdout);
}

int main(){
    char img_content[200];

    printf("Press Enter to read data...\n");
    getchar(); // Wait for Enter key press

    FILE* file = fopen("image", "r");
    if(file == NULL) {
        printf("No File!\n");
        return 0;
    }

    fread(img_content, 1, 200, file);
    fclose(file);

    dotty(img_content);

    printf("App Exit~\n");
    return 0;
}
